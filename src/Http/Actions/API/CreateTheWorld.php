<?php


namespace EmilysWorld\Http\Actions\API;


use EmilysWorld\Domain\World\Commands\CreateWorld;
use League\Tactician\CommandBus;
use Slim\Http\Request;
use Slim\Http\Response;

class CreateTheWorld
{
    /** @var CommandBus */
    private $commandBus;

    /**
     * CreateTheWorld constructor.
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @param string   $name
     * @param Request  $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(string $name, Request $request, Response $response): Response
    {
        $command = new CreateWorld($name);

        $this->commandBus->handle($command);

        return $response->withJson([
            'success' => true,
            'worldName' => $command->getWorldName()
        ]);
    }
}