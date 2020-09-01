<?php
namespace EmilysWorld\Infrastructure\Doctrine\framework;


interface DomainEvent extends \JsonSerializable
{
    /**
     * Given the body of a domain event that's been serialized, this method should reconstitute the event into
     * a proper DomainEvent object of the same type.
     *
     * @param array $body
     *
     * @return DomainEvent
     */
    public static function deserialize(array $body);

    /**
     * @return string the event name in dot notation
     */
    public function getEventName();
}
