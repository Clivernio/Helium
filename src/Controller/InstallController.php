<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Exception\InvalidRequest;
use App\Module\Install as InstallModule;
use App\Repository\ConfigRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
        ]);
    }

    /**
     * Install API Endpoint.
     *
     * Adds the options and admin user account
     */
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

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('install-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        // Install application
        $this->installModule->installApplication([
            'he_app_installed'           => 'done',
            'he_app_name'                => $data->appName,
            'he_app_url'                 => $data->appURL,
            'he_app_email'               => $data->appEmail,
            'he_app_home_layout'         => 'default',
            'he_google_analytics_code'   => '',
            'he_mailer_provider'         => 'disabled',
            'he_mailer_dsn'              => 'null://null',
            'he_seo_description'         => "",
            'he_seo_keywords'            => "",
            'he_seo_canonical'           => $data->appURL,
            'he_seo_twitter_title'       => "",
            'he_seo_twitter_description' => "",
            'he_seo_twitter_image'       => "",
            'he_seo_twitter_site'        => "",
            'he_seo_twitter_creator'     => "",
            'he_newsletter_title'        => "Subscribe Today",
            'he_newsletter_description'  => "Never miss a news",
            'he_newsletter_footer'       => "We won’t send you spam. Unsubscribe at any time.",
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
