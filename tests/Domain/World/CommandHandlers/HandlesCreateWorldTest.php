<?php


namespace EmilysWorld\Tests\Domain\World\CommandHandlers;


use EmilysWorld\Domain\World\CommandHandlers\HandlesCreateWorld;
use EmilysWorld\Domain\World\Commands\CreateWorld;
use EmilysWorld\Domain\World\Entities\World;
use EmilysWorld\Domain\World\Repositories\WorldsRepository;
use EmilysWorld\Tests\BaseTestCase;
use Psr\Log\LoggerInterface;

class HandlesCreateWorldTest extends BaseTestCase
{
    public function testSanity(): void
    {
        $name = 'new world';
        $command = new CreateWorld(
            $name
        );

        $worldRepository = new WorldsRepository(static::$em, static::$eventDispatcher);

        $handler = new HandlesCreateWorld($worldRepository, static::$container->get(LoggerInterface::class));
        $result = $handler->handle($command);

        $imFound = $worldRepository->findByWorldName($name);

        $this->assertInstanceOf(World::class, $imFound);

        $this->assertTrue($result);
    }
}