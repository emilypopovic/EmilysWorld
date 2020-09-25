<?php

namespace EmilysWorld\Infrastructure\Messaging;

/**
 * For use by commands that are to not be durable (persisted to disk)
 * @package Infrastructure\Messaging
 */
interface TransientCommand extends Command
{

}