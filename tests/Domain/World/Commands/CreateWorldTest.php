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
        $this->assertEquals(CreateWorld::ROUTING_KEY, $command->getCommandName());


        $encoded = json_encode($command->jsonSerialize());
        var_dump($encoded);

        $decoded = json_decode($encoded, true);
        var_dump($decoded);

        $deserialize = CreateWorld::deserialize($decoded);
        var_dump($deserialize);


        var_dump('========');

        var_dump($deserialize->jsonSerialize());


    }
}