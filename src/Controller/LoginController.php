<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Exception\InvalidRequest;
use App\Module\Auth as AuthModule;
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
 * Login Controller.
 */
class LoginController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var AuthModule */
    private $authModule;

    /** @var Validator */
    private $validator;

    /** @var InstallModule */
    private $installModule;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        AuthModule $authModule,
        Validator $validator,
        InstallModule $installModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->authModule       = $authModule;
        $this->validator        = $validator;
        $this->installModule    = $installModule;
    }

    /**
     * Login Web Page.
     */
    public function login(): Response
    {
        $this->logger->info("Render login page");

        // Redirect to install page
        if (!$this->installModule->isInstalled()) {
            $this->logger->info("Application is not installed");

            return $this->redirectToRoute('app_ui_install');
        }

        return $this->render('page/login.html.twig', [
            'title' => $this->translator->trans("Login") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'is_demo'        => isset($_ENV['DEMO_MODE']),
        ]);
    }

    /**
     * Login API Endpoint.
     */
    public function loginEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/loginAction.schema.json"
        );

        $this->logger->info("Trigger login v1 endpoint");

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('login-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info(sprintf(
            "Authenticate the user %s",
            $data->email
        ));

        $result = $this->authModule->loginAction(
            $data->email,
            $data->password
        );

        $this->logger->info(sprintf(
            "User %s logged in successfully",
            $data->email
        ));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'User logged in successfully.'
            ),
        ]);
    }
}
