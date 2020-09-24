<?php


namespace EmilysWorld\Domain\World\CommandHandlers;


use EmilysWorld\Domain\World\Entities\World;
use EmilysWorld\Domain\World\Repositories\WorldsRepository;
use EmilysWorld\Domain\World\Commands\CreateWorld;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class HandlesCreateWorld
{
    /** @var WorldsRepository */
    private $worldsRepository;

    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * HandlesCreateLead constructor.
     *
     * @param WorldsRepository $integrationsRepository
     * @param LoggerInterface        $logger
     */
    public function __construct(
        WorldsRepository $integrationsRepository,
        LoggerInterface $logger
    )
    {
        $this->worldsRepository = $integrationsRepository;
        $this->logger           = $logger;
    }

    /**
     * @param CreateWorld $command
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handle(CreateWorld $command): bool
    {
        $world = $this->worldsRepository->findByWorldName($command->getWorldName());
        if($world !== null) {
            echo "\nWorld with that name has already been created!\n";
            return true;
        }

        $newWorld = new World(
            Uuid::uuid4(),
            $command->getWorldName(),
            'planet'
        );

        //store in repo
        return $this->worldsRepository->store($newWorld);
    }
}