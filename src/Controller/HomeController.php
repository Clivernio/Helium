<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Exception\InvalidRequest;
use App\Module\Install as InstallModule;
use App\Module\Subscriber as SubscriberModule;
use App\Repository\ConfigRepository;
use App\Repository\SubscriberRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Home Controller.
 */
class HomeController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var SubscriberModule */
    private $subscriberModule;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        InstallModule $installModule,
        SubscriberModule $subscriberModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->installModule    = $installModule;
        $this->subscriberModule = $subscriberModule;
    }

    /**
     * Home Web Page.
     */
    #[Route('/', name: 'app_ui_home')]
    public function home(): Response
    {
        $this->logger->info("Render home page");

        // Redirect to install page
        if (!$this->installModule->isInstalled()) {
            $this->logger->info("Application is not installed");

            return $this->redirectToRoute('app_ui_install');
        }

        $layout = $this->configRepository->findValueByName("he_app_home_layout", "default");

        return $this->render(sprintf('page/home.%s.html.twig', $layout), [
            'title'          => $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
        ]);
    }

    /**
     * Subscribe API Endpoint.
     */
    #[Route('/api/v1/subscribe', name: 'app_endpoint_v1_subscribe')]
    public function subscribeEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger subscribe v1 endpoint");

        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/subscribeAction.schema.json"
        );

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('subscribe-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->subscriberModule->add([
            'email'  => $data->email,
            'status' => SubscriberRepository::PENDING_VERIFY,
        ]);

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Email subscribed successfully. Please check your inbox to verify!'
            ),
        ]);
    }
}
