<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Entity\Config;
use App\Repository\ConfigRepository;
use Psr\Log\LoggerInterface;

/**
 * Settings Module.
 */
class Settings
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository
    ) {
        $this->logger           = $logger;
        $this->configRepository = $configRepository;
    }

    /**
     * Update Settings.
     */
    public function update(array $data): void
    {
        foreach ($data as $key => $value) {
            $config = $this->configRepository->findOne($key);

            if (empty($config)) {
                $this->logger->info(sprintf("Create a config with key %s", $key));

                $config = Config::fromArray([
                    'name'     => $key,
                    'value'    => $value,
                    'autoload' => 'on',
                ]);
            } else {
                $this->logger->info(sprintf("Update a config with key %s", $key));
                $config->setValue($value);
                $config->setUpdatedAt(new \DateTimeImmutable());
            }

            $this->logger->info(sprintf("Store a config with key %s", $key));
            $this->configRepository->save($config, true);
        }
    }
}
