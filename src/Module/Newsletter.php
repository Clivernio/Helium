<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Repository\ConfigRepository;
use Psr\Log\LoggerInterface;

/**
 * Newsletter Module.
 */
class Newsletter
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
}
