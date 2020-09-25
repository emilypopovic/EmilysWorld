<?php

use Doctrine\ORM\EntityManager;
use EmilysWorld\Base\EmilysApp;
use EmilysWorld\Domain\World\Commands\CreateWorld;
use EmilysWorld\Infrastructure\Messaging\CommandBus;
use Ramsey\Uuid\Uuid;

include_once __DIR__ . '/../config/bootstrap.php';

$app = new EmilysApp();
$entityManager = $app->getContainer()->get(EntityManager::class);
$commandBus = $app->getContainer()->get(CommandBus::class);


$newWorldName = $argv[1];
$uuid = Uuid::uuid4();

$world = new CreateWorld(
    $newWorldName
);

$commandBus->handle($world);

//$entityManager->persist($world);
//$entityManager->flush();

echo 'Created world with name ' . $world->getWorldName() . "\n";

