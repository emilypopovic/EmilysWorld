<?php


namespace EmilysWorld\Domain\World\Commands;


use EmilysWorld\Infrastructure\Messaging\Command;

class CreateWorld implements Command
{
    const ROUTING_KEY = 'world.create';

    /**
     * @var string
     */
    private $worldName;

    /**
     * CreateWorld constructor.
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
    public function getCommandName()
    {
        return static::ROUTING_KEY;
    }

    /**
     * @inheritDoc
     */
    public static function deserialize(array $data): self
    {
        return new static(
            $data['worldName']
        );
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'worldName' => $this->getWorldName()
        ];
    }
}