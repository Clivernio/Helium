<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

use App\Repository\NewsletterRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Newsletter Entity.
 */
#[ORM\Table(name: 'he_newsletter')]
#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
class Newsletter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: NewsletterMeta::class, mappedBy: 'newsletter', cascade: ['ALL'])]
    private Collection $metas;

    #[ORM\OneToMany(targetEntity: Delivery::class, mappedBy: 'newsletter', cascade: ['ALL'])]
    private Collection $deliveries;

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
     * Set a Name.
     *
     * @param string
     *
     * @return Option
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get a Name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
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
     * @return Newsletter
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Create a newsletter from an array.
     */
    public static function fromArray(array $data): Newsletter
    {
        return (new Newsletter())
            ->setName($data['name'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTimeImmutable() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTimeImmutable() : $data['updatedAt']);
    }
}
