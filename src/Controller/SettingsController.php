<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Settings as SettingsModule;
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
 * Settings Controller.
 */
class SettingsController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var SettingsModule */
    private $settingsModule;

    /** @var Validator */
    private $validator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        SettingsModule $settingsModule,
        Validator $validator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->settingsModule   = $settingsModule;
        $this->validator        = $validator;
    }

    /**
     * Settings Web Page.
     */
    #[Route('/admin/settings', name: 'app_ui_settings')]
    public function settings(): Response
    {
        $this->logger->info("Render settings page");

        return $this->render('page/settings.html.twig', [
            'title' => $this->translator->trans("General Settings") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code'  => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'app_name'        => $this->configRepository->findValueByName("he_app_name", ""),
            'app_url'         => $this->configRepository->findValueByName("he_app_url", ""),
            'app_email'       => $this->configRepository->findValueByName("he_app_email", ""),
            'app_home_layout' => $this->configRepository->findValueByName("he_app_home_layout", ""),
            'mailer_provider' => $this->configRepository->findValueByName("he_mailer_provider", ""),
            'mailer_dsn'      => $this->configRepository->findValueByName("he_mailer_dsn", ""),
            'user'            => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
        ]);
    }

    /**
     * Settings API Endpoint.
     */
    #[Route('/admin/api/v1/settings', name: 'app_endpoint_v1_settings', methods: ['POST'])]
    public function settingsEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/updateSettingsAction.schema.json"
        );

        $this->logger->info("Trigger settings v1 endpoint");

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('settings-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info("Updating settings");

        // Update settings
        $this->settingsModule->update([
            'he_app_name'              => $data->appName,
            'he_app_url'               => $data->appURL,
            'he_app_email'             => $data->appEmail,
            'he_app_home_layout'       => $data->appLayout,
            'he_google_analytics_code' => $data->appGoogleTrackingCode,
            'he_mailer_provider'       => $data->appMailerProdvider,
            'he_mailer_dsn'            => $data->appMailerDSN,
        ]);

        $this->logger->info("Settings updated");

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Settings updated successfully.'
            ),
        ]);
    }
}
