<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

/**
 * Task Entity.
 */
class Task
{
    /**
     * @var int
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private ?string $uuid = null;

    /**
     * @var string
     */
    private ?string $status = null;

    /**
     * @var string
     */
    private ?string $payload = null;

    /**
     * @var string
     */
    private ?string $result = null;

    /**
     * @var DateTime
     */
    private ?\DateTime $runAt = null;

    /**
     * @var DateTime
     */
    private ?\DateTime $createdAt = null;

    /**
     * @var DateTime
     */
    private ?\DateTime $updatedAt = null;

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
     * @return DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set Created At.
     *
     * @param \DateTime
     *
     * @return DateTime
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get Updated At.
     *
     * @return DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set Updated At.
     *
     * @param \DateTime
     *
     * @return Task
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get Run At.
     *
     * @return DateTime
     */
    public function getRunAt(): ?\DateTime
    {
        return $this->runAt;
    }

    /**
     * Set Run At.
     *
     * @param \DateTime
     *
     * @return Task
     */
    public function setRunAt(\DateTime $runAt): self
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
            ->setRunAt(empty($data['runAt']) ? new \DateTime() : $data['runAt'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTime() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTime() : $data['updatedAt']);
    }
}
