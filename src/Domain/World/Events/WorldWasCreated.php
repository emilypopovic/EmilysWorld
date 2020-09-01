<?php


namespace EmilysWorld\Domain\World\Events;


use EmilysWorld\Infrastructure\Doctrine\framework\DomainEvent;

class WorldWasCreated implements DomainEvent
{
    const ROUTING_KEY = 'emilys_world.was_created';

    /**
     * @var string
     */
    private $worldName;

    /**
     * SendCustomerReportEmail constructor.
     *
     * @param string $worldName
     */
    public function __construct(string $worldName)
    {
        $this->worldName = $worldName;
    }

    /**
     * @return string
     */
    public function getWorldName(): string
    {
        return $this->worldName;
    }

    /**
     * @inheritDoc
     */
    public static function deserialize(array $data)
    {
        return new static(
            $data['worldName']
        );    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'worldName' => $this->getWorldName()
        ];
    }

    public function getEventName()
    {
        return static::ROUTING_KEY;
    }
}