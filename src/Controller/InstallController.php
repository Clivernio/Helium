<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Weekly project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Repository\OptionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Install Controller.
 */
class InstallController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OptionRepository */
    private $optionRepository;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository,
        TranslatorInterface $translator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->optionRepository = $optionRepository;
    }

    /**
     * Install Web Page.
     */
    #[Route('/install', name: 'app_install_web')]
    public function installPage(): Response
    {
        $this->logger->info("Render install page");

        return $this->render('page/install.html.twig', [
            'title' => "Weekly",
        ]);
    }

    /**
     * Install API Endpoint.
     */
    #[Route('/api/v1/install', name: 'app_install_v1_endpoint')]
    public function installEndpoint(): JsonResponse
    {
        $this->logger->info("Trigger install endpoint");

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Application installed successfully.'
            ),
        ]);
    }
}
