<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Message\Ping;
use App\Repository\ConfigRepository;
use App\Service\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Health Controller.
 */
class HealthController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Worker */
    private $worker;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        Worker $worker
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->worker           = $worker;
    }

    /**
     * Health Endpoint.
     */
    #[Route('/_health', name: 'app_health_api_endpoint')]
    public function health(): JsonResponse
    {
        $this->logger->info("Trigger health check");

        $this->worker->dispatch(new Ping(), ["message" => "pong"]);

        return $this->json([
            'status' => 'ok',
        ]);
    }
}
