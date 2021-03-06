<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Exception\InvalidRequest;
use App\Message\VerifyEmail;
use App\Module\Install as InstallModule;
use App\Module\Subscriber as SubscriberModule;
use App\Repository\ConfigRepository;
use App\Repository\SubscriberRepository;
use App\Service\Validator;
use App\Service\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /** @var Worker */
    private $worker;

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
        SubscriberModule $subscriberModule,
        Worker $worker,
        Validator $validator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->installModule    = $installModule;
        $this->subscriberModule = $subscriberModule;
        $this->worker           = $worker;
        $this->validator        = $validator;
    }

    /**
     * Home Web Page.
     */
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
            'title'                  => $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code'         => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'newsletter_title'       => $this->configRepository->findValueByName("he_newsletter_title", ""),
            'newsletter_description' => $this->configRepository->findValueByName("he_newsletter_description", ""),
            'newsletter_footer'      => $this->configRepository->findValueByName("he_newsletter_footer", ""),
            "meta"                   => [
                "description"         => $this->configRepository->findValueByName("he_seo_description", ""),
                "keywords"            => $this->configRepository->findValueByName("he_seo_keywords", ""),
                "canonical"           => $this->configRepository->findValueByName("he_seo_canonical", ""),
                "twitter_title"       => $this->configRepository->findValueByName("he_seo_twitter_title", ""),
                "twitter_description" => $this->configRepository->findValueByName("he_seo_twitter_description", ""),
                "twitter_image"       => $this->configRepository->findValueByName("he_seo_twitter_image", ""),
                "twitter_site"        => $this->configRepository->findValueByName("he_seo_twitter_site", ""),
                "twitter_creator"     => $this->configRepository->findValueByName("he_seo_twitter_creator", ""),
            ],
        ]);
    }

    /**
     * Verify Subscriber.
     */
    public function verifySubscriber(string $email, string $token): Response
    {
        $this->logger->info(sprintf("Verify subscriber with email %s and token %s", $email, $token));

        $result = $this->subscriberModule->verifySubscriber($email, $token);

        if (!$result) {
            throw new NotFoundHttpException(sprintf("Subscriber with email %s not found", $email));
        }

        $subscriber = $this->subscriberModule->findOneByEmail(
            $email
        );

        $this->subscriberModule->edit(
            $subscriber->getId(),
            ['status' => SubscriberRepository::SUBSCRIBED]
        );

        return $this->render('page/verify.html.twig', [
            'title'          => $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            "meta"           => [
                "description"         => $this->configRepository->findValueByName("he_seo_description", ""),
                "keywords"            => $this->configRepository->findValueByName("he_seo_keywords", ""),
                "canonical"           => $this->configRepository->findValueByName("he_seo_canonical", ""),
                "twitter_title"       => $this->configRepository->findValueByName("he_seo_twitter_title", ""),
                "twitter_description" => $this->configRepository->findValueByName("he_seo_twitter_description", ""),
                "twitter_image"       => $this->configRepository->findValueByName("he_seo_twitter_image", ""),
                "twitter_site"        => $this->configRepository->findValueByName("he_seo_twitter_site", ""),
                "twitter_creator"     => $this->configRepository->findValueByName("he_seo_twitter_creator", ""),
            ],
        ]);
    }

    /**
     * Unsubscribe Page.
     */
    public function unsubscribe(string $email, string $token): Response
    {
        $this->logger->info(sprintf("Verify subscriber with email %s and token %s", $email, $token));

        $result = $this->subscriberModule->verifySubscriber($email, $token);

        if (!$result) {
            throw new NotFoundHttpException(sprintf("Subscriber with email %s not found", $email));
        }

        $subscriber = $this->subscriberModule->findOneByEmail(
            $email
        );

        if (SubscriberRepository::SUBSCRIBED !== $subscriber->getStatus()) {
            return $this->redirectToRoute('app_ui_home');
        }

        return $this->render('page/unsubscribe.html.twig', [
            'title'                  => $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code'         => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'newsletter_title'       => $this->configRepository->findValueByName("he_newsletter_title", ""),
            'newsletter_description' => $this->configRepository->findValueByName("he_newsletter_description", ""),
            'newsletter_footer'      => $this->configRepository->findValueByName("he_newsletter_footer", ""),
            "meta"                   => [
                "description"         => $this->configRepository->findValueByName("he_seo_description", ""),
                "keywords"            => $this->configRepository->findValueByName("he_seo_keywords", ""),
                "canonical"           => $this->configRepository->findValueByName("he_seo_canonical", ""),
                "twitter_title"       => $this->configRepository->findValueByName("he_seo_twitter_title", ""),
                "twitter_description" => $this->configRepository->findValueByName("he_seo_twitter_description", ""),
                "twitter_image"       => $this->configRepository->findValueByName("he_seo_twitter_image", ""),
                "twitter_site"        => $this->configRepository->findValueByName("he_seo_twitter_site", ""),
                "twitter_creator"     => $this->configRepository->findValueByName("he_seo_twitter_creator", ""),
            ],
            'subscriber' => [
                'id'    => $subscriber->getId(),
                'email' => $subscriber->getEmail(),
                'token' => $subscriber->getToken(),
            ],
        ]);
    }

    /**
     * Subscribe API Endpoint.
     */
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

        $subscriber = $this->subscriberModule->findOneByEmail($data->email);

        if (empty($subscriber)) {
            // Add a new subscriber
            $subscriber = $this->subscriberModule->add([
                'email'  => $data->email,
                'status' => SubscriberRepository::PENDING_VERIFY,
            ]);
        } else {
            // Edit current subscriber
            $this->subscriberModule->edit(
                $subscriber->getId(),
                [
                    'status' => SubscriberRepository::PENDING_VERIFY,
                ]
            );
        }

        // Dispatch verify email task
        $this->worker->dispatch(
            new VerifyEmail(),
            [
                "email" => $subscriber->getEmail(),
                "token" => $subscriber->getToken(),
            ]
        );

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Email added successfully. Please check your inbox to verify!'
            ),
        ]);
    }

    /**
     * Unsubscribe API Endpoint.
     */
    public function unsubscribeEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger unsubscribe v1 endpoint");

        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/unsubscribeAction.schema.json"
        );

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('unsubscribe-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $result = $this->subscriberModule->verifySubscriber($data->email, $data->token);

        if (!$result) {
            throw new InvalidRequest('Invalid request');
        }

        $subscriber = $this->subscriberModule->findOneByEmail($data->email);

        if (empty($subscriber)) {
            throw new InvalidRequest('Invalid request');
        }

        // Unsubscribe
        $this->subscriberModule->edit(
            $subscriber->getId(),
            [
                'status' => SubscriberRepository::UNSUBSCRIBED,
            ]
        );

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Email unsubscribed successfully!'
            ),
        ]);
    }
}
