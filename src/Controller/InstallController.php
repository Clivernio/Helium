<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Install as InstallModule;
use App\Repository\ConfigRepository;
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

    /** @var ConfigRepository */
    private $configRepository;

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
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        InstallModule $installModule,
        Validator $validator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
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

        // Redirect to 404 if installed
        if ($this->installModule->isInstalled()) {
            $this->logger->info("Application is already installed, Redirect to 404");

            return $this->redirectToRoute('app_ui_not_found');
        }

        return $this->render('page/install.html.twig', [
            'title' => $this->translator->trans("Install") . " | "
            . $this->configRepository->findValueByName("mw_app_name", "Midway"),
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

        // stop the call if app is already installed
        if ($this->installModule->isInstalled()) {
            $this->logger->error("Application is already installed");

            return $this->json([
                'errorMessage' => $this->translator->trans(
                    'Application is already installed!'
                ),
            ]);
        }

        $this->logger->info("Install the application");

        $data = json_decode($content);

        // Install application
        $this->installModule->installApplication([
            'mw_app_installed' => "done",
            'mw_app_name'      => $data->appName,
            'mw_app_url'       => $data->appURL,
            'mw_app_email'     => $data->appEmail,
        ]);

        // Create admin account
        $this->installModule->createAdmin(
            $data->adminEmail,
            $data->adminPassword
        );

        $this->logger->info("Application installed successfully");

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Application installed successfully.'
            ),
        ]);
    }
}
