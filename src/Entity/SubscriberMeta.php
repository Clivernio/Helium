<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

/**
 * SubscriberMeta Entity.
 */
class SubscriberMeta
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
    private ?string $value = null;

    /**
     * @var Subscriber
     */
    private $subscriber;

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
     * Get a Name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set a Name.
     *
     * @param string
     *
     * @return SubscriberMeta
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get a Value.
     *
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set a Value.
     *
     * @param string
     *
     * @return SubscriberMeta
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get Subscriber.
     *
     * @return Subscriber
     */
    public function getSubscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    /**
     * Set Subscriber.
     *
     * @param Subscriber
     *
     * @return SubscriberMeta
     */
    public function setSubscriber(?Subscriber $subscriber): self
    {
        $this->subscriber = $subscriber;

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
     * @return SubscriberMeta
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Create an option from an array.
     */
    public static function fromArray(array $data): SubscriberMeta
    {
        return (new SubscriberMeta())
            ->setName($data['name'])
            ->setValue($data['value'])
            ->setSubscriber($data['subscriber'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTime() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTime() : $data['updatedAt']);
    }
}
