<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

/**
 * Delivery Entity.
 */
class Delivery
{
    /**
     * @var int
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private ?string $status = null;

    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @var Newsletter
     */
    private $newsletter;

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
     * @return Delivery
     */
    public function setSubscriber(?Subscriber $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * Get Newsletter.
     *
     * @return Newsletter
     */
    public function getNewsletter(): ?Newsletter
    {
        return $this->newsletter;
    }

    /**
     * Set Newsletter.
     *
     * @param Newsletter
     *
     * @return Delivery
     */
    public function setNewsletter(?Newsletter $newsletter): self
    {
        $this->newsletter = $newsletter;

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
     * @return Delivery
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Create an option from an array.
     */
    public static function fromArray(array $data): Delivery
    {
        return (new Delivery())
            ->setStatus($data['status'])
            ->setSubscriber($data['subscriber'])
            ->setNewsletter($data['newsletter'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTime() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTime() : $data['updatedAt']);
    }
}
