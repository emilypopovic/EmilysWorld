<?php

use Doctrine\ORM\EntityManager;
use EmilysWorld\Base\EmilysApp;
use EmilysWorld\Domain\World\Entities\World;
use Ramsey\Uuid\Uuid;

include_once __DIR__ . '/../config/bootstrap.php';

$app = new EmilysApp();
$entityManager = $app->getContainer()->get(EntityManager::class);

$newWorldName = $argv[1];
$uuid = Uuid::uuid4();

$world = new World(
    $uuid,
    $newWorldName,
    'Planet'
);

$entityManager->persist($world);
$entityManager->flush();

echo 'Created world with id ' . $world->getId() . "\n";

