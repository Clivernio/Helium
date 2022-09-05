<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Repository\OptionRepository;
use Psr\Log\LoggerInterface;

/**
 * Install Module.
 */
class Install
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OptionRepository */
    private $optionRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository
    ) {
        $this->logger           = $logger;
        $this->optionRepository = $optionRepository;
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
     * Install the app.
     */
    public function install(array $data): bool
    {
        foreach ($data as $key => $value) {
            // code...
        }
    }
}
