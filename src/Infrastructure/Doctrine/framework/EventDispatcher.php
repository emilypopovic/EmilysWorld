<?php


namespace EmilysWorld\Infrastructure\Doctrine\framework;


interface EventDispatcher
{
    /**
     * @param DomainEvent $event
     *
     * @return void
     */
    public function dispatch(DomainEvent $event);

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     */
    public function addListener($eventName, $listener);

    /**
     * @param $eventName
     * @return void
     */
    public function removeListeners($eventName);

    /**
     * @param $eventName
     * @return array
     */
    public function getListeners($eventName);
}