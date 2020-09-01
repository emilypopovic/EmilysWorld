<?php


namespace EmilysWorld\Domain\World\Entities;



use EmilysWorld\Infrastructure\Doctrine\framework\AbstractEntity;
use Ramsey\Uuid\UuidInterface;

class World extends AbstractEntity
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;

    /**
     * World constructor.
     * @param UuidInterface   $id
     * @param string $name
     * @param string $type
     */
    public function __construct(
        UuidInterface $id,
        string $name,
        string $type
    ){
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

}