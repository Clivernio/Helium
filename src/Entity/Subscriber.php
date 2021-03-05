<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * Subscriber Entity.
 */
class Subscriber
{
    /**
     * @var int
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private ?string $email = null;

    /**
     * @var string
     */
    private ?string $token = null;

    /**
     * @var string
     */
    private ?string $status = null;

    private Collection $metas;

    private Collection $deliveries;

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
     * Get Email.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set an Email.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Status.
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
     * @return Newsletter
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get Token.
     *
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set a Token.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get Metas.
     *
     * @return Collection
     */
    public function getMetas(): ?Collection
    {
        return $this->metas;
    }

    /**
     * Get Deliveries.
     *
     * @return Collection
     */
    public function getDeliveries(): ?Collection
    {
        return $this->deliveries;
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
     * @return Subscriber
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Create an option from an array.
     */
    public static function fromArray(array $data): Subscriber
    {
        return (new Subscriber())
            ->setEmail($data['email'])
            ->setStatus($data['status'])
            ->setToken($data['token'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTime() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTime() : $data['updatedAt']);
    }
}
