<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Entity\UserMeta;
use App\Exception\InvalidRequest;
use App\Message\ResetPasssword;
use App\Repository\ConfigRepository;
use App\Repository\UserMetaRepository;
use App\Repository\UserRepository;
use App\Security\Authenticator;
use App\Service\Worker;
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

    /** @var UserMetaRepository */
    private $userMetaRepository;

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    /** @var Authenticator */
    private $authenticator;

    /** @var Worker */
    private $worker;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        UserRepository $userRepository,
        UserMetaRepository $userMetaRepository,
        UserPasswordHasherInterface $passwordHasher,
        Authenticator $authenticator,
        Worker $worker
    ) {
        $this->logger             = $logger;
        $this->configRepository   = $configRepository;
        $this->userRepository     = $userRepository;
        $this->userMetaRepository = $userMetaRepository;
        $this->passwordHasher     = $passwordHasher;
        $this->authenticator      = $authenticator;
        $this->worker             = $worker;
    }

    /**
     * Login Action.
     */
    public function loginAction(string $email, string $plainPassword): void
    {
        $user = $this->authenticator->findUserByEmail($email);

        if (empty($user)) {
            throw new InvalidRequest('Invalid email or password.');
        }

        if (!$this->authenticator->validatePassword($user, $plainPassword)) {
            throw new InvalidRequest('Invalid email or password.');
        }

        $this->authenticator->initSession($user);
    }

    /**
     * Reset User Password by a Token.
     */
    public function resetPasswordAction(string $token, string $newPassword): void
    {
        $meta = $this->userMetaRepository->findMetaByValue($token);

        if (empty($meta)) {
            throw new InvalidRequest('Invalid reset password request');
        }

        $this->authenticator->updatePassword($meta->getUser(), $newPassword);

        $this->userMetaRepository->remove($meta, true);
    }

    /**
     * Forgot Password Request.
     */
    public function forgotPasswordAction(string $email): void
    {
        // Create a random token
        $token = bin2hex(random_bytes(rand(30, 40)));

        while (!empty($this->userMetaRepository->findMetaByValue($token))) {
            $token = bin2hex(random_bytes(rand(30, 40)));
        }

        $user = $this->authenticator->findUserByEmail($email);

        if (empty($user)) {
            throw new InvalidRequest('Invalid email provided.');
        }

        // Create reset token & attach to user
        $meta = UserMeta::fromArray([
            "name"  => "reset_password_token",
            "value" => $token,
            "user"  => $user,
        ]);

        $this->userMetaRepository->save($meta, true);

        // Dispatch email task
        $this->worker->dispatch(
            new ResetPasssword(),
            ["email" => $email, "token" => $token]
        );
    }
}
