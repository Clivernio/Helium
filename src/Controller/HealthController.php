<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Repository\OptionRepository;
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

    /** @var OptionRepository */
    private $optionRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Worker */
    private $worker;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository,
        TranslatorInterface $translator,
        Worker $worker
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->optionRepository = $optionRepository;
        $this->worker           = $worker;
    }

    /**
     * Health Endpoint.
     */
    #[Route('/_health', name: 'app_health_api_endpoint')]
    public function health(): JsonResponse
    {
        $this->logger->info("Trigger health check");

        $this->worker->ping();

        return $this->json([
            'status' => 'ok',
        ]);
    }
}
