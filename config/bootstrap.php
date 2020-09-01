<?php

use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (!defined('ENV_LOADED')) {
    $rootDir = __DIR__ . '/..';

    if (file_exists($rootDir . '/.env')) {
        $dotEnv = new Dotenv($rootDir);

        try {
            $dotEnv->load();
        } catch (Exception $exception) {

        }
    }
    define('ENV_LOADED', true);
}
