<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Settings as SettingsModule;
use App\Repository\ConfigRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        SettingsModule $settingsModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->settingsModule   = $settingsModule;
    }

    /**
     * Settings Web Page.
     */
    #[Route('/admin/settings/general', name: 'app_ui_general_settings')]
    public function settings(): Response
    {
        $this->logger->info("Render settings page");

        return $this->render('page/settings.html.twig', [
            'title' => $this->translator->trans("General Settings") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'tab' => 'general',
        ]);
    }

    /**
     * Appearance Web Page.
     */
    #[Route('/admin/settings/appearance', name: 'app_ui_appearance_settings')]
    public function appearance(): Response
    {
        $this->logger->info("Render appearance page");

        return $this->render('page/settings.html.twig', [
            'title' => $this->translator->trans("Appearance Settings") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'tab' => 'appearance',
        ]);
    }

    /**
     * Settings API Endpoint.
     */
    #[Route('/api/v1/settings', name: 'app_endpoint_v1_settings', methods: ['POST'])]
    public function settingsEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/settingsAction.schema.json"
        );

        $this->logger->info("Trigger settings v1 endpoint");

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('settings-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info("Updating settings");

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Settings updated successfully.'
            ),
        ]);
    }
}
