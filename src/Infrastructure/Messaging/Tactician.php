<?php

namespace EmilysWorld\Infrastructure\Messaging;


use League\Tactician\CommandBus as BaseBus;

/**
 * Class Tactician
 * @package Infrastructure\Messaging\Adapters
 * @since v1.0.0
 */
class Tactician implements CommandBus
{
    /**
     * @var BaseBus
     */
    private $bus;

    /**
     * @param BaseBus $bus
     */
    public function __construct(BaseBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @inheritdoc
     */
    public function handle(Command $command)
    {
        return $this->bus->handle($command);
    }
}