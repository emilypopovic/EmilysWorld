<?php


namespace EmilysWorld\Domain\World\Repositories;


use EmilysWorld\Domain\World\Entities\World;
use EmilysWorld\Infrastructure\Doctrine\framework\AbstractRepository;
use Ramsey\Uuid\Uuid;

class WorldsRepository extends AbstractRepository
{
    /**
     * @inheritDoc
     */
    public function find(Uuid $id)
    {
        return $this->entityManager->find(World::class, $id);
    }

    /**
     * @param string $worldName
     * @return World|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByWorldName(string $worldName): ?World
    {
        $qb = $this->entityManager->createQueryBuilder();
        $query = $qb->select('w')
            ->from(World::class, 'w')
            ->where($qb->expr()->eq('w.name', ':name'))
            ->setParameter(':name', $worldName);
        return $query->getQuery()->getOneOrNullResult();
    }

}