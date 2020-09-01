<?php


namespace EmilysWorld\Tests;


use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use EmilysWorld\Base\EmilysApp;
use EmilysWorld\Domain\World\Entities\World;
use EmilysWorld\Infrastructure\Doctrine\framework\CustomEventDispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class BaseTestCase extends TestCase
{
//    /** @var Generator $faker */
//    protected static $faker;

    /** @var ContainerInterface $container */
    protected static $container;

    /** @var CustomEventDispatcher $eventDispatcher */
    protected static $eventDispatcher;

    /** @var EntityManager $em */
    protected static $em;

    /** @var EmilysApp $app */
    protected static $app;

    /**
     * @throws ORMException
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::$app = new EmilysApp();

        static::$container = static::$app->getContainer();

        $cache  = new ArrayCache();
        $config = $config = Setup::createXMLMetadataConfiguration(
            static::$container->get('settings.doctrine.xml'));
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setAutoGenerateProxyClasses(true);

        static::$em = EntityManager::create(
            [
                'url' => 'sqlite:///:memory:',
            ],
            $config
        );

        // Generate tables based on schema
        $schemaTool = new SchemaTool(static::$em);
        $classes    = static::$em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);
        $platform = static::$em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        static::$eventDispatcher = new CustomEventDispatcher(static::$container);
    }

    public function setUp(): void
    {
        parent::setUp();

    }

//    /**
//     * @return MockInterface|LoggerInterface
//     */
//    protected function getLogger()
//    {
//        return \Mockery::mock(LoggerInterface::class);
//    }

    public static function createWorld(): World
    {
        return new World(
            Uuid::uuid4(),
            'pluto',
            'planet'
        );
    }
}