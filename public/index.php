<?php

use EmilysWorld\Base\EmilysApp;

include_once __DIR__ . '/../config/bootstrap.php';

$app = new EmilysApp();

include_once __DIR__ . '/../config/routes.php';


echo "hi bitch\n";


$app->run();


