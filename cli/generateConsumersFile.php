<?php

use EmilysWorld\Base\EmilysApp;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Supervisor\Configuration\Configuration;
use Supervisor\Configuration\Section\Program;
use Supervisor\Configuration\Writer\IniFileWriter;

include_once __DIR__ . '/../config/bootstrap.php';

$app = new EmilysApp();
$container = $app->getContainer();

$commandMappings = include __DIR__ . '/../config/command_mappings.php';

$filesystem = new Filesystem(new Local('/etc/supervisor/conf.d'));
$writer = new IniFileWriter($filesystem,'consumers.conf');
$configuration = new Configuration();

foreach ($commandMappings as $command => $handler) {
    $cmdName = defined($command . '::ROUTING_KEY') ? $command::ROUTING_KEY : $command::COMMAND_NAME;
    $section = new Program('emilysWorld_' . $cmdName, [
        'directory' => __DIR__ . '/../',
        'command' => 'php cli/consumeCommands.php ' . $cmdName,
        'numprocs' => 1,
        'process_name' => '%(program_name)s_%(process_num)02d',
        'startretries' => 500,
        'autostart' => true,
        'autorestart' => true,
        'user' => 'emily'
    ]);
    $configuration->addSection($section);
}

$writer->write($configuration);

