<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Entity\User;
use App\Exception\ResourceNotFound;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;

/**
 * Profile Module.
 */
class Profile
{
    /** @var LoggerInterface */
    private $logger;

    /** @var UserRepository */
    private $userRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository
    ) {
        $this->logger         = $logger;
        $this->userRepository = $userRepository;
    }

    /**
     * Update User Profile.
     */
    public function updateProfile(int $userId, array $data): void
    {
        $user = $this->userRepository->findOneByID($userId);

        if (empty($user)) {
            throw new ResourceNotFound(sprintf("User with id %s not found", $userId));
        }

        $user->setEmail($data["email"]);
        $user->setFirstName($data["firstName"]);
        $user->setLastName($data["lastName"]);
        $user->setJob($data["jobTitle"]);
        $user->setUpdatedAt(new \DateTimeImmutable());

        $this->userRepository->add($user, true);
    }
}
