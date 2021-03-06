<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Entity\Config;
use App\Entity\User;
use App\Repository\ConfigRepository;
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

    /** @var ConfigRepository */
    private $configRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->logger           = $logger;
        $this->configRepository = $configRepository;
        $this->userRepository   = $userRepository;
        $this->passwordHasher   = $passwordHasher;
    }

    /**
     * Check if the app is installed.
     */
    public function isInstalled(): bool
    {
        $value = $this->configRepository->findValueByName('he_app_installed', 'false');

        return 'false' === $value ? false : true;
    }

    /**
     * Install the App.
     */
    public function installApplication(array $data): void
    {
        foreach ($data as $key => $value) {
            $config = Config::fromArray([
                'name'     => $key,
                'value'    => $value,
                'autoload' => 'on',
            ]);

            $this->configRepository->save($config, true);
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
        $user->setFirstName("Joe");
        $user->setLastName("Doe");
        $user->setJob("Software Engineer");
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setPassword($hashedPassword);
        $this->userRepository->add($user, true);
    }
}
