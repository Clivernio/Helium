<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Repository\OptionRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    /** @var OptionRepository */
    private $optionRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Validator */
    private $validator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository,
        TranslatorInterface $translator,
        Validator $validator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->optionRepository = $optionRepository;
        $this->validator        = $validator;
    }

    /**
     * Forgot Password Web Page.
     */
    #[Route('/forgot-password', name: 'app_ui_forgot_password')]
    public function fpwd(): Response
    {
        $this->logger->info("Render forgot password page");

        return $this->render('page/fpwd.html.twig', [
            'title' => $this->translator->trans("Forgot Password") . " | "
            . $this->optionRepository->findValueByKey("mw_app_name", "Midway"),
        ]);
    }

    /**
     * Forgot Password API Endpoint.
     */
    #[Route('/api/v1/forgot-password', name: 'app_endpoint_v1_forgot_password', methods: ['POST'])]
    public function forgotPasswordEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate($content, "v1/forgotPasswordAction.schema.json");

        $this->logger->info("Trigger forgot password v1 endpoint");

        return $this->json([]);
    }
}
