<?php

use AVReviews\Infrastructure\SlimErrorHandler;
use Bunny\Client;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use EmilysWorld\Infrastructure\Consumer\CommandQueueConsumer;
use EmilysWorld\Infrastructure\Doctrine\framework\CustomEventDispatcher;
use EmilysWorld\Infrastructure\Doctrine\framework\EventDispatcher;
use EmilysWorld\Infrastructure\Messaging\Middleware\CommandQueueMiddleware;
use EmilysWorld\Infrastructure\Messaging\Middleware\EntityManagerMiddleware;
use EmilysWorld\Infrastructure\Messaging\RabbitMQ;
use EmilysWorld\Infrastructure\Messaging\Tactician;
use League\Tactician\CommandBus;
use League\Tactician\Container\ContainerLocator;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Plugins\LockingMiddleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV'));
}

if (!defined('PRODUCT_NAME')) {
    define('PRODUCT_NAME', 'emilys-world');
}

if (!defined('BASE_URL')) {
    define('BASE_URL', getenv('BASE_URL'));
}

$settings = [
    'product_name'                 => PRODUCT_NAME,
    'settings.displayErrorDetails' => true,
    'log.threshold'                => Logger::DEBUG,

    // rabbit
    'rabbit.username'              => getenv('RABBIT_USERNAME'),
    'rabbit.password'              => getenv('RABBIT_PASSWORD'),
    'rabbit.host'                  => getenv('RABBIT_HOST'),
    'rabbit.port'                  => getenv('RABBIT_PORT'),
    'rabbit.vhost'                 => getenv('RABBIT_VHOST'),

    // command bus
    'settings.command_bus.exchange_name'             => PRODUCT_NAME . '_events_exchange',
    'settings.command_bus.dead_letter_exchange_name' => PRODUCT_NAME . '_events_dlx_exchange',
    'settings.command_handler_mappings'  => function (ContainerInterface $container) {
        return include __DIR__ . '/command_mappings.php';
    },
    'doctrine.config'                    => function (ContainerInterface $container) {
        return include __DIR__ . '/doctrine.php';
    },
    'settings.doctrine.xml'              => function (ContainerInterface $container) {
        return include __DIR__ . '/doctrine_paths.php';
    },

    EntityManagerInterface::class => function (ContainerInterface $container) {
        return $container->get(EntityManager::class);
    },
    EntityManager::class => function (ContainerInterface $container) {
        $entityManager = null;

        $cache  = new ArrayCache();
        $config = Setup::createXMLMetadataConfiguration(
            $container->get('settings.doctrine.xml'),
            false,
            null,
            $cache
        );

        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setAutoGenerateProxyClasses(true);

        $entityManager = EntityManager::create(
            $container->get('doctrine.config'),
            $config
        );

        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        return $entityManager;
    },
    CommandBus::class => function (ContainerInterface $container) {
        $commandMappings = $container->get('settings.command_handler_mappings');
        $containerLocator = new ContainerLocator( //Fetch handler instances from mappings
            $container,
            $commandMappings
        );
        $commandHandlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(), //Extract the name from a command so that the name can be determined
            $containerLocator,
            new HandleInflector() //Deduce method name to call on command handler -> Handle command by calling "handle" method
        );

        $rabbitMQ = $container->get(RabbitMQ::class);
        $commandBus = new CommandBus(
            [
                new LockingMiddleware(),
                new CommandQueueMiddleware($rabbitMQ, $container->get('settings.command_bus.exchange_name')),
                new EntityManagerMiddleware($container->get(EntityManager::class)),
                $commandHandlerMiddleware,
            ]
        );
        return new Tactician($commandBus);
    },
    Logger::class => function (ContainerInterface $container) {
        $log = new Logger(PRODUCT_NAME);
        $log->pushHandler(new StreamHandler(__DIR__ . '/../logs/logs.log', $container->get('log.threshold')));
        return $log;
    },
    LoggerInterface::class => function (ContainerInterface $container) {
        return $container->get(Logger::class);
    },
    EventDispatcher::class => function (ContainerInterface $container) {
        $dispatcher = new CustomEventDispatcher($container);
        include __DIR__ . '/event_subscriber_mappings.php';
        return $dispatcher;
    },
    CommandQueueConsumer::class => function (ContainerInterface $container) {
        $commandBus = $container->get(CommandBus::class);
        $commandMappings = [];
        $containerMappings = $container->get('settings.command_handler_mappings');
        foreach ($containerMappings as $command => $handler) {
            $commandMappings[$command::ROUTING_KEY] = [
                'command' => $command,
                'handler' => $handler,
            ];
        }
        $connection = [
            'host' => $container->get('rabbit.host'),
            'vhost' => $container->get('rabbit.vhost'),
            'user' => $container->get('rabbit.username'),
            'password' => $container->get('rabbit.password'),
        ];
        $bunny = new Client($connection);
        $bunny->connect();
        $channel = $bunny->channel();
        $consumer = new CommandQueueConsumer(
            $channel,
            $commandMappings,
            $commandBus,
            $container->get('settings.command_bus.exchange_name'),
            $container->get('settings.command_bus.dead_letter_exchange_name'),
            $container->get(Logger::class),
            $container->get(EntityManager::class)
        );
        return $consumer;
    },
    RabbitMQ::class => function (ContainerInterface $container) {
        $adapter = new RabbitMQ(
            $container->get('rabbit.host'),
            $container->get('rabbit.vhost'),
            $container->get('rabbit.username'),
            $container->get('rabbit.password')
        );

        return $adapter;
    }
];

if (APP_ENV !== 'development') {
    $settings['errorHandler'] = function (ContainerInterface $c) {
        return $c->get(SlimErrorHandler::class);
    };

    $settings['phpErrorHandler'] = function (ContainerInterface $c) {
        return $c->get('errorHandler');
    };

    $settings['displayErrorDetails'] = false;
}

return $settings;