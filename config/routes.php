<?php

/** @var EmilysApp $app */

use EmilysWorld\Base\EmilysApp;
use EmilysWorld\Http\Actions\API\CreateTheWorld;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/hello/{name}', function (Request $request, Response $response, string $name) {
    $response->getBody()->write("Hello there, $name");
    return $response;
});

$app->get('/health', function(Request $request, Response $response) {
    return $response->write('hi glenn')->withStatus(200);
});

$app->group('/world', function() {
    $this->post('/create/{name}', CreateTheWorld::class);
});

