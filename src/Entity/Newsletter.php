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

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $deliveryStatus = null; // ON_HOLD, PENDING, IN_PROGRESS, FINISHED

    #[ORM\Column(length: 100)]
    private ?string $deliveryType = null; // DRAFT, NOW, SCHEDULED

    #[ORM\Column(length: 200)]
    private ?string $template = null;

    #[ORM\Column(length: 200)]
    private ?string $from = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $deliveryTime = null;

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
     * @return Newsletter
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
     * Set a Delivery Status.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setDeliveryStatus(string $deliveryStatus): self
    {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    /**
     * Get a Delivery Status.
     *
     * @return string
     */
    public function getDeliveryStatus(): ?string
    {
        return $this->deliveryStatus;
    }

    /**
     * Set a Delivery Type.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setDeliveryType(string $deliveryType): self
    {
        $this->deliveryType = $deliveryType;

        return $this;
    }

    /**
     * Get a Delivery Type.
     *
     * @return string
     */
    public function getDeliveryType(): ?string
    {
        return $this->deliveryType;
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
     * Get Delivery Time.
     *
     * @return DateTimeImmutable
     */
    public function getDeliveryTime(): ?\DateTimeImmutable
    {
        return $this->deliveryTime;
    }

    /**
     * Set Delivery Time.
     *
     * @param \DateTimeImmutable
     *
     * @return DateTimeImmutable
     */
    public function setDeliveryTime(\DateTimeImmutable $deliveryTime): self
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * Set a Template.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get a Template.
     *
     * @return string
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * Set a From.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setFrom(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get a From.
     *
     * @return string
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * Set a Content.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get a Content.
     *
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
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
            ->setDeliveryStatus($data['deliveryStatus'])
            ->setFrom($data['from'])
            ->setDeliveryType($data['deliveryType'])
            ->setDeliveryTime($data['deliveryTime'])
            ->setTemplate($data['template'])
            ->setContent($data['content'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTimeImmutable() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTimeImmutable() : $data['updatedAt']);
    }
}
