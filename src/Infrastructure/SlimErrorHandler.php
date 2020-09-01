<?php


namespace AVReviews\Infrastructure;


use Bugsnag\Client;
use Slim\Http\Request;
use Slim\Http\Response;
use Throwable;

class SlimErrorHandler
{
    protected $bugsnag;

    /**
     * SlimErrorHandler constructor.
     * @param Client $bugsnag
     */
    public function __construct(Client $bugsnag)
    {
        $this->bugsnag = $bugsnag;
    }

    /**
     * @param Request        $request
     * @param Response       $response
     * @param Throwable|null $exception
     * @return Response
     */
    public function __invoke(Request $request, Response $response, Throwable $exception = null): Response
    {
        $this->bugsnag->notifyException($exception);
        return $response->withStatus(500);
    }
}