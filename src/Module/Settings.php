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
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Settings Module.
 */
class Settings
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** KernelInterface $appKernel */
    private $appKernel;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        KernelInterface $appKernel
    ) {
        $this->logger           = $logger;
        $this->configRepository = $configRepository;
        $this->appKernel        = $appKernel;
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
                $config->setUpdatedAt(new \DateTime());
            }

            $this->logger->info(sprintf("Store a config with key %s", $key));
            $this->configRepository->save($config, true);
        }
    }

    /**
     * Get Home Layouts.
     */
    public function getHomeLayouts(): array
    {
        $result       = [];
        $basePath     = rtrim($this->appKernel->getProjectDir(), "/");
        $templatePath = sprintf("%s/templates/default/page", $basePath);
        $templates    = scandir($templatePath);

        foreach ($templates as $template) {
            if (false !== strpos($template, "home.")) {
                $items    = explode(".", $template);
                $name     = ucwords(str_replace("_", " ", $items[1]));
                $result[] = ['id' => $items[1], 'name' => $name];
            }
        }

        return $result;
    }
}
