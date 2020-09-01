<?php
/**
 * Created by PhpStorm.
 * User: bradleyhanebury
 * Date: 2018-01-10
 * Time: 5:39 PM
 */

namespace EmilysWorld\Infrastructure\Doctrine\framework;


/**
 * Class ImmediateEvent
 *
 * @package Infrastructure\Messaging
 */
class ConsumedEvent implements DomainEvent
{
    const EXCEPTION_MESSAGE = "If you're using deserialize here, then you're doing something wrong!";

    /**
     * @var DomainEvent
     */
    private $event;

    /**
     * ImmediateEvent constructor.
     *
     * @param DomainEvent $event
     */
    public function __construct(DomainEvent $event)
    {
        $this->event = $event;
    }


    /**
     * @return DomainEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @inheritDoc
     */
    public function getEventName()
    {
        return $this->getEvent()->getEventName();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'event' => $this->getEvent()->jsonSerialize()
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