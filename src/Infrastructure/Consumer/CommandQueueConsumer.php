<?php


namespace EmilysWorld\Infrastructure\Consumer;


use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;
use Doctrine\ORM\EntityManagerInterface;
use EmilysWorld\Infrastructure\Messaging\Command;
use EmilysWorld\Infrastructure\Messaging\CommandBus;
use EmilysWorld\Infrastructure\Messaging\ImmediateCommand;
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

        $startTime = time();

        $this->channel->run(
            function (Message $message, Channel $channel, Client $bunny) use ($startTime, $routingKey) {

                $pid = getmypid();

                //we kill our processes roughly every hour. This is due to the way MySQL / RDS handles long running connections (it kills them...)
                if (time() - $startTime > 3550) {
                    $this->logger->debug("{$pid} Ending process after running for 3550 seconds");
                    $channel->nack($message, false, true);
                    $channel->close();
                    die();
                }

                //we look for this here to help prevent it from trickling down to a command handler if it's not necessary
                if ($this->entityManager->isOpen() !== true) {
                    $this->logger->debug("{$pid} Ending process because the entity manager is closed");
                    $channel->nack($message, false, true);
                    $channel->close();
                    die();
                }

                try {
                    $success = $this->handleMessage($message);

                    if ($success) {
                        $channel->ack($message);
                    }
                    else {
                        throw new \Exception("{$pid} Command handler return false for message: " . json_encode([
                                'content' => $message->content,
                                'routingKey' => $message->routingKey,
                                'headers' => $message->headers
                            ]));
                    }
                }
                catch (\Exception $exception) {
                    $this->logger->debug("{$pid} general command exception", [
                        'content' => $message->content,
                        'routingKey' => $message->routingKey,
                        'headers' => $message->headers,
                        'exceptionMessage' => $exception->getMessage(),
                        'exceptionTrace' => $exception->getTraceAsString()
                    ]);
                    $channel->nack($message, false, false);
                }
                catch (\Throwable $exception) {
                    $this->logger->debug("{$pid} throwable exception", [
                        'content' => $message->content,
                        'routingKey' => $message->routingKey,
                        'headers' => $message->headers,
                        'exceptionMessage' => $exception->getMessage(),
                        'exceptionTrace' => $exception->getTraceAsString()
                    ]);
                    $channel->nack($message, false, false);
                }
            },
            $queueName
        );


    }

    protected function handleMessage(Message $message) {

        $body = $message->content;
        $decoded = json_decode($body, true);

        if(!is_array($decoded)){
            return false;
        }

        /**
         * deserialize our original command
         * @var Command $className
         */
        $className = $this->commandMappings[$message->routingKey]['command'];
        $originalCommand = $className::deserialize($decoded);

        //re-wrap inside a QueuedCommand so that it won't get re-queued and send off to command bus
        $rabbitMQCommand = new ImmediateCommand($originalCommand);

        return (bool)$this->commandBus->handle($rabbitMQCommand);
    }
}