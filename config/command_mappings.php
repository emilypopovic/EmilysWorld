<?php

use EmilysWorld\Domain\World\CommandHandlers\HandlesCreateWorld;
use EmilysWorld\Domain\World\Commands\CreateWorld;

return [
    CreateWorld::class => HandlesCreateWorld::class
];
