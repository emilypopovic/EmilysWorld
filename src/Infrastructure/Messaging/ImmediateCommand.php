<?php


namespace EmilysWorld\Infrastructure\Messaging;


class ImmediateCommand implements Command
{
    const EXCEPTION_MESSAGE = "If you're using deserialize here, then you're doing something wrong!";
    /**
     * @var Command
     */
    private $command;

    /**
     * QueuedCommand constructor.
     *
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @inheritDoc
     */
    public function getCommandName()
    {
        return $this->getCommand()->getCommandName();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'command' => $this->getCommand()->jsonSerialize()
        ];
    }

    /**
     * @inheritDoc
     * @throws \RuntimeException
     */
    public static function deserialize(array $data)
    {
        throw new \RuntimeException(self::EXCEPTION_MESSAGE);
    }
}