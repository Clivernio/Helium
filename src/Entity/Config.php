<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

/**
 * Config Entity.
 */
class Config
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
     * @var string
     */
    private ?string $autoload = null;

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
     * @return Config
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
     * @return Config
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get Autoload.
     *
     * @return string
     */
    public function getAutoload(): ?string
    {
        return $this->autoload;
    }

    /**
     * Set Autoload.
     *
     * @param string
     *
     * @return Config
     */
    public function setAutoload(?string $autoload): self
    {
        $this->autoload = $autoload;

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
     * @return Config
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Create an option from an array.
     */
    public static function fromArray(array $data): Config
    {
        return (new Config())
            ->setName($data['name'])
            ->setValue($data['value'])
            ->setAutoload($data['autoload'])
            ->setCreatedAt(empty($data['createdAt']) ? new \DateTime() : $data['createdAt'])
            ->setUpdatedAt(empty($data['updatedAt']) ? new \DateTime() : $data['updatedAt']);
    }
}
