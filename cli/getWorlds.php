<?php

use EmilysWorld\Domain\World\Entities\World;
use Doctrine\ORM\EntityManager;
use EmilysWorld\Base\EmilysApp;

include_once __DIR__ . '/../config/bootstrap.php';

$app = new EmilysApp();
$entityManager = $app->getContainer()->get(EntityManager::class);

$worldRepository = $entityManager->getRepository(World::class);
$worlds = $worldRepository->findAll();

foreach ($worlds as $world) {
    echo sprintf("-%s\n", $world->getName());
}

