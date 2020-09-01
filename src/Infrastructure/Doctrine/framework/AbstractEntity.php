<?php

namespace EmilysWorld\Infrastructure\Doctrine\framework;

use Ramsey\Uuid\Uuid;

/**
 * Class AbstractEntity
 * @package Infrastructure\Persistence
 * @Entity
 */
abstract class AbstractEntity
{
    /**
     * @var Uuid
     * @Id()
     * @Column(type="guid")
     * @deprecated 4.2.3
     * @since
     */
    protected $uuid;


    /** @var DomainEvent[] */
    private $events = []; //need to set this here b/c doctrine :D

    /**
     * @param DomainEvent $event
     */
    protected function raise(DomainEvent $event) {
        $this->events[] = $event;
    }

    /**
     * @return DomainEvent[]
     */
    public function getEvents() {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    /**
     * @return Uuid
     * @deprecated 4.2.3
     */
    public function getUuid()
    {
        return $this->uuid;
    }

}
