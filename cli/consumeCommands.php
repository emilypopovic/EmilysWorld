<?php

use EmilysWorld\Base\EmilysApp;
use EmilysWorld\Infrastructure\Consumer\CommandQueueConsumer;

require_once dirname(__DIR__) . '/config/bootstrap.php';

ini_set('MEMORY_LIMIT', '-1');

$routingKey = $argv[1];

if (empty($routingKey)) {
    die('Error: parameter 1 must be the routing key');
}

$app = new EmilysApp();
$container = $app->getContainer();

$consumer = $container->get(CommandQueueConsumer::class);
$consumer->run($routingKey, $routingKey, $routingKey . '_dlx');

