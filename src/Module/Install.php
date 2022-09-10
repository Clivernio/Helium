<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Entity\Option;
use App\Entity\User;
use App\Repository\OptionRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Install Module.
 */
class Install
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

    /**
     * Check if the app is installed.
     */
    public function isInstalled(): bool
    {
        $value = $this->optionRepository->findValueByKey('mw_app_installed', 'false');

        return 'false' === $value ? false : true;
    }

    /**
     * Install the App.
     */
    public function installApplication(array $data): void
    {
        foreach ($data as $key => $value) {
            $option = Option::fromArray([
                'key'      => $key,
                'value'    => $value,
                'autoload' => 'on',
            ]);

            $this->optionRepository->save($option);
        }
    }

    /**
     * Create Admin Account.
     *
     * @param string $email
     * @param string $password
     */
    public function createAdmin($email, $password): void
    {
        $user = new User();
        $user->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $this->userRepository->add($user, true);
    }
}
