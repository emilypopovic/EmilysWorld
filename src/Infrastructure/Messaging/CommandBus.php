<?php

namespace EmilysWorld\Infrastructure\Messaging;

/**
 * Interface CommandBus
 * @package Infrastructure\Messaging
 * @since v1.0.0
 */
interface CommandBus
{
    /**
     * @param Command $command
     *
     * @return boolean
     */
    public function handle(Command $command);
}
