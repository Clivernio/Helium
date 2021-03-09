<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subscriber Entity.
 */
#[ORM\Table(name: 'he_subscriber')]
#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 30)]
    private ?string $status = null;

    #[ORM\OneToMany(targetEntity: SubscriberMeta::class, mappedBy: 'subscriber', cascade: ['ALL'])]
    private Collection $metas;

    #[ORM\OneToMany(targetEntity: Delivery::class, mappedBy: 'subscriber', cascade: ['ALL'])]
    private Collection $deliveries;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

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
     * @return Subscriber
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get Removed At.
     *
     * @return DateTimeImmutable
     */
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * Set Removed At.
     *
     * @param \DateTimeImmutable
     *
     * @return Subscriber
     */
    public function setDeletedAt(\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTimeImmutable() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTimeImmutable() : $data['updatedAt'])
            ->setDeletedAt(empty($data['deletedAt']) ? new \DateTimeImmutable() : $data['deletedAt']);
    }
}
