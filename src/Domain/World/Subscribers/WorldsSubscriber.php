<?php


namespace EmilysWorld\Domain\World\Subscribers;


use EmilysWorld\Domain\World\Commands\CreateWorld;
use EmilysWorld\Domain\World\Events\WorldWasCreated;
use EmilysWorld\Infrastructure\Messaging\CommandBus;
use Psr\Log\LoggerInterface;

class WorldsSubscriber
{
    /** @var CommandBus */
    private $commandBus;

    /** @var LoggerInterface */
    private $logger;

    /**
     * WorldsSubscriber constructor.
     * @param CommandBus              $commandBus
     * @param LoggerInterface         $logger
     */
    public function __construct(
        CommandBus $commandBus,
        LoggerInterface $logger
    )
    {
        $this->commandBus        = $commandBus;
        $this->logger            = $logger;
    }

    public function onWorldWasCreated(WorldWasCreated $event): void
    {
        $this->commandBus->handle(new CreateWorld($event->getWorldName()));
    }
}