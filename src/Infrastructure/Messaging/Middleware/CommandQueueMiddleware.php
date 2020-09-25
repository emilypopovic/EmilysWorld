<?php


namespace EmilysWorld\Infrastructure\Messaging\Middleware;


use EmilysWorld\Infrastructure\Messaging\Command;
use EmilysWorld\Infrastructure\Messaging\ImmediateCommand;
use EmilysWorld\Infrastructure\Messaging\MessageDispatcher;
use EmilysWorld\Infrastructure\Messaging\TransientCommand;
use League\Tactician\Middleware;
use PhpAmqpLib\Message\AMQPMessage;

class CommandQueueMiddleware implements Middleware
{
    /**
     * @var MessageDispatcher
     */
    private $adapter;

    private $exchange;

    /**
     * QueueMiddleware constructor.
     *
     * @param MessageDispatcher $adapter
     * @param string $exchange
     */
    public function __construct(MessageDispatcher $adapter, $exchange = '')
    {
        $this->adapter = $adapter;
        $this->exchange = $exchange;
    }


    /**
     * @param Command $command
     * @param callable $next
     *
     * @return bool
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof ImmediateCommand) {
            return $next($command->getCommand());
        }

        $this->adapter->publish(
            json_encode($command->jsonSerialize()),
            $this->getOptions($command),
            $this->exchange,
            $command->getCommandName()
        );

        return true;
    }

    private function getOptions(Command $command) : array
    {
        if($command instanceof TransientCommand) {
            return [];
        }

        return [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ];
    }
}