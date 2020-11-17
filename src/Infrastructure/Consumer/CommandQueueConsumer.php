<?php


namespace EmilysWorld\Infrastructure\Consumer;


use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Doctrine\ORM\EntityManagerInterface;
use EmilysWorld\Infrastructure\Messaging\ImmediateCommand;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;

class CommandQueueConsumer
{
    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var array the full class name of the command expected to be received
     */
    protected $commandMappings;

    /**
     * @var CommandBus
     */
    protected $commandBus;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var string
     */
    private $deadLetterExchangeName;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        Channel $channel,
        $commandMappings,
        CommandBus $commandBus,
        $exchangeName,
        $deadLetterExchangeName,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    )
    {
        $this->channel = $channel;
        $this->commandMappings = $commandMappings;
        $this->commandBus = $commandBus;
        $this->exchangeName = $exchangeName;
        $this->deadLetterExchangeName = $deadLetterExchangeName;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function run(string $routingKey, string $queueName, string $queueDlx)
    {
        $this->channel->exchangeDeclare($this->exchangeName, 'topic', false, true);
        $this->channel->exchangeDeclare($this->deadLetterExchangeName, 'topic', false, true);

        //define our DLX which is based off of the routing key name. This is for clarity.
        $dlQueueName = $queueDlx;
        $this->channel->queueDeclare($dlQueueName, '', true);
        $this->channel->queueBind($dlQueueName, $this->deadLetterExchangeName, $routingKey);

        //NOTE our Queue name will always be the same as the routing key. This is for clarity.
        $this->channel->queueDeclare($queueName, '', true, false, false, false, [
            'x-dead-letter-exchange' => $this->deadLetterExchangeName
        ]);
        $this->channel->queueBind($queueName, $this->exchangeName, $routingKey);


        //TODO: get the queue working

        $startTime = time();

        $this->channel->run(
            function (Message $message, Channel $channel, Client $bunny) use ($startTime, $routingKey) {

                $success = $this->handleMessage($message); // Handle your message here

                if ($success) {
                    $channel->ack($message); // Acknowledge message
                    return;
                }

                $channel->nack($message);
            },
            $queueName
        );


    }

    protected function handleMessage(Message $message): bool
    {
        $body = $message->content;
        $decoded = json_decode($body, true);

        if(!is_array($decoded)){
            return false;
        }

        $className = $this->commandMappings[$message->routingKey]['command'];
        $originalCommand = $className::deserialize($decoded);

        //re-wrap inside a QueuedCommand so that it won't get re-queued and send off to command bus
        $rabbitMQCommand = new ImmediateCommand($originalCommand);
        return (bool)$this->commandBus->handle($rabbitMQCommand);
    }
}