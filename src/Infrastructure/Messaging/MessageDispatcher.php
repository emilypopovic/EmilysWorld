<?php

namespace EmilysWorld\Infrastructure\Messaging;


interface MessageDispatcher
{
    /**
     * @param string $body
     * @param array $headers
     * @param string $exchange
     * @param string $routingKey
     *
     * @return bool
     */
    public function publish($body, array $headers = [], $exchange, $routingKey);
}