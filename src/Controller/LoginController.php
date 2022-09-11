<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Auth as AuthModule;
use App\Repository\OptionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Login Controller.
 */
class LoginController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OptionRepository */
    private $optionRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var AuthModule */
    private $authModule;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository,
        TranslatorInterface $translator,
        AuthModule $authModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->optionRepository = $optionRepository;
        $this->authModule       = $authModule;
    }

    /**
     * Login Web Page.
     */
    #[Route('/login', name: 'app_ui_login')]
    public function login(): Response
    {
        $this->logger->info("Render login page");

        return $this->render('page/login.html.twig', [
            'title' => $this->translator->trans("Login") . " | "
            . $this->optionRepository->findValueByKey("mw_app_name", "Midway"),
        ]);
    }

    /**
     * Login API Endpoint.
     */
    #[Route('/api/v1/login', name: 'app_endpoint_v1_login', methods: ['POST'])]
    public function loginEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate($content, "v1/loginAction.schema.json");

        $this->logger->info("Trigger login v1 endpoint");

        $data = json_decode($content);

        $this->logger->info(sprintf("Authenticate the user %s", $data->email));

        $this->authModule->loginAction(
            $data->email,
            $data->password
        );

        $this->logger->info(sprintf("User %s logged in successfully", $data->email));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'User logged in successfully.'
            ),
        ]);
    }
}
