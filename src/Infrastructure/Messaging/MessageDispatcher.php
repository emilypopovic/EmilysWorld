<?php

namespace EmilysWorld\Infrastructure\Messaging;


/**
 * Interface MessageDispatcher
 * @package Infrastructure\Messaging
 * @since v1.0.0
 */
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
