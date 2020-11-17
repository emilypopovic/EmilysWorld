<?php

use EmilysWorld\Domain\World\Commands\CreateWorld;
use Doctrine\ORM\EntityManager;
use EmilysWorld\Base\EmilysApp;
use League\Tactician\CommandBus;

include_once __DIR__ . '/../config/bootstrap.php';

$app = new EmilysApp();
$entityManager = $app->getContainer()->get(EntityManager::class);
$commandBus = $app->getContainer()->get(CommandBus::class);

$newWorldName = $argv[1];

//call command to create new world
$commandBus->handle(new CreateWorld($newWorldName));

