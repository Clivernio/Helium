<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Exception\InvalidRequest;
use App\Module\Subscriber as SubscriberModule;
use App\Repository\ConfigRepository;
use App\Repository\SubscriberRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Subscriber Controller.
 */
class SubscriberController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var SubscriberModule */
    private $subscriberModule;

    /** @var Validator */
    private $validator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        SubscriberModule $subscriberModule,
        Validator $validator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->subscriberModule = $subscriberModule;
        $this->validator        = $validator;
    }

    /**
     * Subscriber Index Web Page.
     */
    public function subscriberIndex(): Response
    {
        $this->logger->info("Render subscriber index page");

        return $this->render('page/subscriber.index.html.twig', [
            'title' => $this->translator->trans("Subscribers") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'user'           => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
        ]);
    }

    /**
     * Subscriber Add Web Page.
     */
    public function subscriberAdd(): Response
    {
        $this->logger->info("Render subscriber add page");

        return $this->render('page/subscriber.add.html.twig', [
            'title' => $this->translator->trans("Subscribers") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'user'           => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
        ]);
    }

    /**
     * Subscriber Edit Web Page.
     */
    public function subscriberEdit(int $id): Response
    {
        $this->logger->info("Render subscriber edit page");

        $subscriber = $this->subscriberModule->findOneById($id);

        if (empty($subscriber)) {
            throw new NotFoundHttpException(sprintf("Subscriber with id %s not found", $id));
        }

        return $this->render('page/subscriber.edit.html.twig', [
            'title' => $this->translator->trans("Subscribers") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'user'           => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
            'subscriber' => [
                'id'     => $subscriber->getId(),
                'email'  => $subscriber->getEmail(),
                'status' => $subscriber->getStatus(),
                'token'  => $subscriber->getToken(),
            ],
        ]);
    }

    /**
     * Subscriber List API Endpoint.
     */
    public function subscriberListEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger subscriber list v1 endpoint");

        $status = !empty($request->get("status")) ? $request->get("status") : "";
        $limit  = !empty($request->get("limit")) ? (int) ($request->get("limit")) : 20;
        $offset = !empty($request->get("offset")) ? (int) ($request->get("offset")) : 0;

        $subscribers = $this->subscriberModule->list($status, $limit, $offset);

        $result = [];

        foreach ($subscribers as $subscriber) {
            $outStatus = str_replace([
                SubscriberRepository::PENDING_VERIFY,
                SubscriberRepository::UNSUBSCRIBED,
                SubscriberRepository::SUBSCRIBED,
                SubscriberRepository::TRASHED,
            ], [
                $this->translator->trans("Pending Verification"),
                $this->translator->trans("Disabled"),
                $this->translator->trans("Enabled"),
                $this->translator->trans("Trashed"),
            ], $subscriber->getStatus());

            $result[] = [
                'id'        => $subscriber->getId(),
                'email'     => $subscriber->getEmail(),
                'status'    => $outStatus,
                'createdAt' => $subscriber->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $subscriber->getUpdatedAt()->format('Y-m-d H:i:s'),
                'editLink'  => $this->generateUrl('app_ui_subscriber_edit', ['id' => $subscriber->getId()]),
            ];
        }

        return $this->json([
            'subscribers' => $result,
            '_metadata'   => [
                'limit'      => $limit,
                'offset'     => $offset,
                'totalCount' => $this->subscriberModule->countByStatus($status),
            ],
        ]);
    }

    /**
     * Subscriber Add API Endpoint.
     */
    public function subscriberAddEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger subscriber add v1 endpoint");

        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/subscriberAddAction.schema.json"
        );

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('subscriber-add-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $subscriber = $this->subscriberModule->add([
            'email'  => $data->email,
            'status' => $data->status,
        ]);

        $this->logger->info(sprintf("Subscriber with id %s got created", $subscriber->getId()));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Subscriber created successfully.'
            ),
        ]);
    }

    /**
     * Subscriber Edit API Endpoint.
     */
    public function subscriberEditEndpoint(Request $request, int $id): JsonResponse
    {
        $this->logger->info("Trigger subscriber edit v1 endpoint");

        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/subscriberEditAction.schema.json"
        );

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('subscriber-edit-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info(sprintf("Update subscriber with id %s", $id));

        if (SubscriberRepository::REMOVED === $data->status) {
            // Delete the subscriber
            $this->subscriberModule->delete($id);

            $this->logger->info(sprintf("Subscriber with id %s got deleted", $id));

            return $this->json([
                'successMessage' => $this->translator->trans(
                    'Subscriber deleted successfully.'
                ),
            ]);
        }

        $this->subscriberModule->edit($id, [
            'email'  => $data->email,
            'status' => $data->status,
        ]);

        $this->logger->info(sprintf("Subscriber with id %s updated", $id));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Subscriber updated successfully.'
            ),
        ]);
    }
}
