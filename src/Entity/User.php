<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User Entity.
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private ?string $email;

    /**
     * @var string
     */
    private ?string $firstName;

    /**
     * @var string
     */
    private ?string $lastName;

    /**
     * @var string
     */
    private ?string $job;

    private array $roles = [];

    /**
     * @var Collection
     */
    private ?Collection $metas = null;

    /**
     * @var string
     */
    private ?string $password;

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
     * Set Email.
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Job.
     *
     * @return string
     */
    public function getJob(): ?string
    {
        return $this->job;
    }

    /**
     * Set Job.
     *
     * @return User
     */
    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get First Name.
     *
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set First Name.
     *
     * @return User
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get Last name.
     *
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set Last Name.
     *
     * @return User
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_ADMIN
        $roles[] = 'ROLE_ADMIN';

        return array_unique($roles);
    }

    /**
     * Set Roles.
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set Password.
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
     * Get Metas.
     *
     * @return Collection
     */
    public function getMetas(): ?Collection
    {
        return $this->metas;
    }
}
