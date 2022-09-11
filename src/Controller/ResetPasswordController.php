<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Auth as AuthModule;
use App\Repository\OptionRepository;
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

    /** @var OptionRepository */
    private $optionRepository;

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
        OptionRepository $optionRepository,
        TranslatorInterface $translator,
        Validator $validator,
        AuthModule $authModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->optionRepository = $optionRepository;
        $this->validator        = $validator;
        $this->authModule       = $authModule;
    }

    /**
     * Reset Password Web Page.
     */
    #[Route('/reset-password/{token}', name: 'app_ui_reset_password')]
    public function fpwd(): Response
    {
        $this->logger->info("Render reset password page");

        return $this->render('page/reset_password.html.twig', [
            'title' => $this->translator->trans("Reset Password") . " | "
            . $this->optionRepository->findValueByKey("mw_app_name", "Midway"),
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
