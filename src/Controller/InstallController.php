<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Install as InstallModule;
use App\Repository\OptionRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /** @var InstallModule */
    private $installModule;

    /** @var Validator */
    private $validator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository,
        TranslatorInterface $translator,
        InstallModule $installModule,
        Validator $validator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->optionRepository = $optionRepository;
        $this->installModule    = $installModule;
        $this->validator        = $validator;
    }

    /**
     * Install Web Page.
     */
    #[Route('/install', name: 'app_ui_install', methods: ['GET', 'HEAD'])]
    public function installPage(): Response
    {
        $this->logger->info("Render install page");

        return $this->render('page/install.html.twig', [
            'title' => $this->optionRepository->findValueByKey("mw_app_name", "Midway"),
        ]);
    }

    /**
     * Install API Endpoint.
     *
     * Adds the options and admin user account
     */
    #[Route('/api/v1/install', name: 'app_endpoint_v1_install', methods: ['POST'])]
    public function installEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate($content, "v1/installAction.schema.json");

        $this->logger->info("Trigger install v1 endpoint");

        if ($this->installModule->isInstalled()) {
            $this->logger->error("Application is already installed");

            return $this->json([
                'errorMessage' => $this->translator->trans(
                    'Application is already installed!'
                ),
            ]);
        }

        $this->logger->info("Install the application");

        $this->installModule->install([
            'mw_app_name' => 'Midway',
        ]);

        $this->logger->info("Application installed successfully");

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Application installed successfully.'
            ),
        ]);
    }
}
