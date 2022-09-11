<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Repository\OptionRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Auth Module.
 */
class Auth
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OptionRepository */
    private $optionRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->logger           = $logger;
        $this->optionRepository = $optionRepository;
        $this->userRepository   = $userRepository;
        $this->passwordHasher   = $passwordHasher;
    }

    public function loginAction(string $email, string $password): bool
    {
        // code...
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
