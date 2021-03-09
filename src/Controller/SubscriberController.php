<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Exception\InvalidRequest;
use App\Exception\ResourceNotFound;
use App\Module\Subscriber as SubscriberModule;
use App\Repository\ConfigRepository;
use App\Repository\SubscriberRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    #[Route('/admin/subscriber', name: 'app_ui_subscriber_index')]
    public function subscriberIndex(): Response
    {
        $this->logger->info("Render subscriber index page");

        return $this->render('page/subscribers.index.html.twig', [
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
    #[Route('/admin/subscriber/add', name: 'app_ui_subscriber_add')]
    public function subscriberAdd(): Response
    {
        $this->logger->info("Render subscriber add page");

        return $this->render('page/subscribers.add.html.twig', [
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
    #[Route('/admin/subscriber/edit/{id}', name: 'app_ui_subscriber_edit')]
    public function subscriberEdit(int $id): Response
    {
        $this->logger->info("Render subscriber edit page");

        $subscriber = $this->subscriberModule->findOneById($id);

        if (empty($subscriber)) {
            throw new ResourceNotFound(sprintf("Subscriber with id %s not found", $id));
        }

        return $this->render('page/subscribers.edit.html.twig', [
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
    #[Route('/api/v1/subscriber', name: 'app_endpoint_v1_subscriber_list', methods: ['GET', 'HEAD'])]
    public function subscriberListEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger subscriber list v1 endpoint");

        $status = !empty($request->get("status")) ? $request->get("status") : "";
        $limit  = !empty($request->get("limit")) ? (int) ($request->get("limit")) : 20;
        $offset = !empty($request->get("offset")) ? (int) ($request->get("offset")) : 0;

        $subscribers = $this->subscriberModule->list($status, $limit, $offset);

        $result = [];

        foreach ($subscribers as $subscriber) {
            $result[] = [
                'id'        => $subscriber->getId(),
                'email'     => $subscriber->getEmail(),
                'status'    => $subscriber->getStatus(),
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
    #[Route('/api/v1/subscriber', name: 'app_endpoint_v1_subscriber_add', methods: ['POST'])]
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
    #[Route('/api/v1/subscriber/{id}', name: 'app_endpoint_v1_subscriber_edit', methods: ['PUT'])]
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

            $this->logger->info(sprintf("Subscriber with id %s deleted", $id));

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
