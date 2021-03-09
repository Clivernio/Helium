<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Repository\ConfigRepository;
use App\Repository\UserRepository;
use App\Security\Authenticator;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Auth Module.
 */
class Auth
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    /** @var Authenticator */
    private $authenticator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        Authenticator $authenticator
    ) {
        $this->logger           = $logger;
        $this->configRepository = $configRepository;
        $this->userRepository   = $userRepository;
        $this->passwordHasher   = $passwordHasher;
        $this->authenticator    = $authenticator;
    }

    /**
     * Login Action.
     */
    public function loginAction(string $email, string $plainPassword): bool
    {
        $user = $this->authenticator->findUserByEmail($email);

        if (!$this->authenticator->validatePassword($user, $plainPassword)) {
            return false;
        }

        $this->authenticator->initSession($user);

        return true;
    }

    public function resetPasswordAction(string $token, string $newPassword): bool
    {
        // code...
    }

    public function forgotPasswordAction(string $email): bool
    {
        // code...
    }
}
