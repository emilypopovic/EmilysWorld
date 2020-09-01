<?php


namespace EmilysWorld\Base;


use DI\Bridge\Slim\App;
use DI\ContainerBuilder;

class EmilysApp extends App
{
    public function configureContainer(ContainerBuilder $builder): void
    {
        $builder->addDefinitions(__DIR__ . '/../../config/di-container.php');
    }
}
