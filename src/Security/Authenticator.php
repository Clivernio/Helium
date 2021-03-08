<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Authenticator.
 */
class Authenticator
{
    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        TokenStorageInterface $tokenStorage,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage          = $tokenStorage;
        $this->passwordHasher        = $passwordHasher;
        $this->userRepository        = $userRepository;
    }

    /**
     * Init a Session.
     */
    public function initSession(UserInterface $user): void
    {
        $token              = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $authenticatedToken = $this->authenticationManager->authenticate($token);
        $this->tokenStorage->setToken($authenticatedToken);
    }

    /**
     * Validate Username and Password.
     */
    public function validatePassword(UserInterface $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }

    /**
     * Update Password.
     */
    public function updatePassword(UserInterface $user, string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);
        $this->userRepository->add($user, true);
    }

    /**
     * Find User By Email.
     */
    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->userRepository->findOneByEmail($email);
    }
}
