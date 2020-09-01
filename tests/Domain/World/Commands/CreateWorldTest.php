<?php


namespace EmilysWorld\Tests\Domain\World\Commands;


use EmilysWorld\Domain\World\Commands\CreateWorld;
use EmilysWorld\Tests\BaseTestCase;

class CreateWorldTest extends BaseTestCase
{
    public function testSanity(): void
    {
        $name = 'new world';

        $command = new CreateWorld(
            $name
        );

        $this->assertEquals($name, $command->getWorldName());
        $this->assertEquals(CreateWorld::COMMAND_NAME, $command->getCommandName());
    }
}