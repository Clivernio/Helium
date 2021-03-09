<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Task Entity.
 */
#[ORM\Table(name: 'he_task')]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $uuid = null;

    #[ORM\Column(length: 30)]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $payload = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $result = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $runAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get a Status.
     *
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set a Status.
     *
     * @param string
     *
     * @return Task
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get a UUID.
     *
     * @return string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Set a UUID.
     *
     * @param string
     *
     * @return Task
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get a Payload.
     *
     * @return string
     */
    public function getPayload(): ?string
    {
        return $this->payload;
    }

    /**
     * Set a Payload.
     *
     * @param string
     *
     * @return Task
     */
    public function setPayload(?string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Get a Result.
     *
     * @return string
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * Set a Result.
     *
     * @param string
     *
     * @return Task
     */
    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get CreatedAt.
     *
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set Created At.
     *
     * @param \DateTimeImmutable
     *
     * @return DateTimeImmutable
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get Updated At.
     *
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set Updated At.
     *
     * @param \DateTimeImmutable
     *
     * @return Task
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get Run At.
     *
     * @return DateTimeImmutable
     */
    public function getRunAt(): ?\DateTimeImmutable
    {
        return $this->runAt;
    }

    /**
     * Set Run At.
     *
     * @param \DateTimeImmutable
     *
     * @return Task
     */
    public function setRunAt(\DateTimeImmutable $runAt): self
    {
        $this->runAt = $runAt;

        return $this;
    }

    /**
     * Create a task from an array.
     */
    public static function fromArray(array $data): Task
    {
        return (new Task())
            ->setStatus($data['status'])
            ->setUuid($data['uuid'])
            ->setPayload($data['payload'])
            ->setResult($data['result'])
            ->setRunAt(empty($data['runAt']) ? new \DateTimeImmutable() : $data['runAt'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTimeImmutable() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTimeImmutable() : $data['updatedAt']);
    }
}
