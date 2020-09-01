<?php


use Doctrine\Common\ClassLoader;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use EmilysWorld\Base\EmilysApp;

include_once __DIR__ . '/config/bootstrap.php';

$app = new EmilysApp();
$entityManager = $app->getContainer()->get(EntityManager::class);

// loads class for migrations
$classLoader = new ClassLoader('Doctrine\DBAL\Migrations', __DIR__ . 'vendor/doctrine/migrations/lib');
$classLoader->register();

//set directory
$configuration = new Configuration($entityManager->getConnection());
$configuration->setMigrationsDirectory('migrations');
$configuration->setMigrationsNamespace('Migrations');
$configuration->setMigrationsTableName('emilys_migrations');

//console commands
$diff = new DiffCommand(); //Generate a migration by comparing your current database to your mapping information
$exec = new ExecuteCommand(); //Execute a single migration version up or down manually
$gen = new GenerateCommand(); //Generate a blank migration class
$migrate = new MigrateCommand(); //Execute a migration to a specified version or the latest available version
$status = new StatusCommand(); //View the status of a set of migrations
$ver = new VersionCommand(); //Manually add and delete migration versions from the version table

//doctrine:migrations:generate
//php vendor/bin/doctrine migrations:migrate

$diff->setMigrationConfiguration($configuration);
$exec->setMigrationConfiguration($configuration);
$gen->setMigrationConfiguration($configuration);
$migrate->setMigrationConfiguration($configuration);
$status->setMigrationConfiguration($configuration);
$ver->setMigrationConfiguration($configuration);

$helperSet = new HelperSet([
    'db' => new ConnectionHelper($entityManager->getConnection()),
    'em' => new EntityManagerHelper($entityManager),
    'dialog' => new QuestionHelper()
]);

$cli = ConsoleRunner::createApplication($helperSet, [
    $diff, $exec, $gen, $migrate, $status, $ver
]);

return $cli->run();
