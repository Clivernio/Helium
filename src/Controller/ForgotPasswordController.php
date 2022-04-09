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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Forgot Password Controller.
 */
class ForgotPasswordController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Validator */
    private $validator;

    /** @var AuthModule */
    private $authModule;

    /** @var InstallModule */
    private $installModule;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        Validator $validator,
        AuthModule $authModule,
        InstallModule $installModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->validator        = $validator;
        $this->authModule       = $authModule;
        $this->installModule    = $installModule;
    }

    /**
     * Forgot Password Web Page.
     */
    #[Route('/forgot-password', name: 'app_ui_forgot_password')]
    public function forgotPassword(): Response
    {
        $this->logger->info("Render forgot password page");

        // Redirect to install page
        if (!$this->installModule->isInstalled()) {
            $this->logger->info("Application is not installed");

            return $this->redirectToRoute('app_ui_install');
        }

        return $this->render('page/forgot_password.html.twig', [
            'title' => $this->translator->trans("Forgot Password") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
        ]);
    }

    /**
     * Forgot Password API Endpoint.
     */
    #[Route('/api/v1/forgot-password', name: 'app_endpoint_v1_forgot_password', methods: ['POST'])]
    public function forgotPasswordEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/forgotPasswordAction.schema.json"
        );

        $this->logger->info("Trigger forgot password v1 endpoint");

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('forgot-password-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info(sprintf(
            "Send a password reset email to %s",
            $data->email
        ));

        $this->authModule->forgotPasswordAction($data->email);

        $this->logger->info(sprintf(
            "An email sent to %s to reset password",
            $data->email
        ));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'An email sent to update your password.'
            ),
        ]);
    }
}
