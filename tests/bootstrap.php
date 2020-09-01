<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (!defined('ENV_LOADED')) {
    $rootDir = __DIR__ . '/..';
    if (file_exists($rootDir . '/.env.test')) {
        $dotEnv = new Dotenv($rootDir);
        $dotEnv->load();
    }
    define('ENV_LOADED', true);
}