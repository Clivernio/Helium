<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Auth as AuthModule;
use App\Repository\ConfigRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Reset Password Controller.
 */
class ResetPasswordController extends AbstractController
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

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        Validator $validator,
        AuthModule $authModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->validator        = $validator;
        $this->authModule       = $authModule;
    }

    /**
     * Reset Password Web Page.
     */
    #[Route('/reset-password/{token}', name: 'app_ui_reset_password')]
    public function resetPassword(): Response
    {
        $this->logger->info("Render reset password page");

        // Redirect to install page
        if (!$this->installModule->isInstalled()) {
            $this->logger->info("Application is not installed");

            return $this->redirectToRoute('app_ui_install');
        }

        return $this->render('page/reset_password.html.twig', [
            'title' => $this->translator->trans("Reset Password") . " | "
            . $this->configRepository->findValueByName("mw_app_name", "Midway"),
        ]);
    }

    /**
     * Reset Password API Endpoint.
     */
    #[Route('/api/v1/reset-password', name: 'app_endpoint_v1_reset_password', methods: ['POST'])]
    public function resetPasswordEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/resetPasswordAction.schema.json"
        );

        $this->logger->info("Trigger reset password v1 endpoint");

        $data = json_decode($content);

        $this->logger->info(sprintf(
            "Reset password for a request with token %s",
            $data->token
        ));

        $this->authModule->resetPasswordAction(
            $data->token,
            $data->newPassword
        );

        $this->logger->info(sprintf(
            "Password got updated for a request with token %s",
            $data->token
        ));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Password updated successfully.'
            ),
        ]);
    }
}
