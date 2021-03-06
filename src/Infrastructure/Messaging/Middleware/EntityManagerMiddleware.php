<?php


use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Infrastructure\Messaging\Command;

class EntityManagerMiddleware implements \League\Tactician\Middleware
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * EntityManagerMiddleware constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Command  $command
     * @param callable $next
     * @return bool
     * @throws MappingException
     */
    public function execute($command, callable $next)
    {
        $this->entityManager->clear();
        return $next($command);
    }

}