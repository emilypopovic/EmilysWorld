<?php

namespace EmilysWorld\Infrastructure\Doctrine\framework;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Ramsey\Uuid\Uuid;

abstract class AbstractRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var  EventDispatcher */
    protected $dispatcher;

    /**
     * AbstractRepository constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcher        $dispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcher $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher    = $dispatcher;
    }

    /**
     * @param AbstractEntity $abstractEntity
     *
     * @return bool
     */
    public function store(AbstractEntity $abstractEntity)
    {
        try {
            $this->entityManager->persist($abstractEntity);
            $this->entityManager->flush();

            $events = $abstractEntity->getEvents();

            foreach ($events as $event) {
                $this->dispatcher->dispatch($event);
            }

            return true;

        } catch (ORMException $exception) {
            return false;
        }
    }

    /**
     * @param Uuid $uuid
     * @return AbstractEntity|object
     */
    abstract public function find(Uuid $uuid);
}