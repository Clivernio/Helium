<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * Newsletter Entity.
 */
class Newsletter
{
    /**
     * @var int
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private ?string $name = null;

    /**
     * @var string
     */
    private ?string $slug = null;

    /**
     * @var string
     */
    private ?string $deliveryStatus = null; // ON_HOLD, PENDING, IN_PROGRESS, FINISHED

    /**
     * @var string
     */
    private ?string $deliveryType = null; // DRAFT, NOW, SCHEDULED

    /**
     * @var string
     */
    private ?string $template = null;

    /**
     * @var string
     */
    private ?string $sender = null;

    /**
     * @var string
     */
    private ?string $content = null;

    /**
     * @var DateTime
     */
    private ?\DateTime $deliveryTime = null;

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
     * Set a Slug.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get a Slug.
     *
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
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
     * @return DateTime
     */
    public function getDeliveryTime(): ?\DateTime
    {
        return $this->deliveryTime;
    }

    /**
     * Set Delivery Time.
     *
     * @param \DateTime
     *
     * @return DateTime
     */
    public function setDeliveryTime(?\DateTime $deliveryTime): self
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
     * Set a Sender.
     *
     * @param string
     *
     * @return Newsletter
     */
    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get a Sender.
     *
     * @return string
     */
    public function getSender(): ?string
    {
        return $this->sender;
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
     * @return Newsletter
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
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
            ->setSlug($data['slug'])
            ->setDeliveryStatus($data['deliveryStatus'])
            ->setSender($data['sender'])
            ->setDeliveryType($data['deliveryType'])
            ->setDeliveryTime($data['deliveryTime'])
            ->setTemplate($data['template'])
            ->setContent($data['content'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTime() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTime() : $data['updatedAt']);
    }
}
