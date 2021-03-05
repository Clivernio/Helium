<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Entity\Config;
use App\Exception\InvalidRequest;
use App\Module\Newsletter as NewsletterModule;
use App\Repository\ConfigRepository;
use App\Repository\NewsletterRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Newsletter Controller.
 */
class NewsletterController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var NewsletterModule */
    private $newsletterModule;

    /** @var Validator */
    private $validator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        NewsletterModule $newsletterModule,
        Validator $validator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->newsletterModule = $newsletterModule;
        $this->validator        = $validator;
    }

    /**
     * Newsletter Web Page.
     */
    #[Route('/admin/newsletter', name: 'app_ui_newsletter_index')]
    public function newsletterIndex(): Response
    {
        $this->logger->info("Render newsletter index page");

        return $this->render('page/newsletter.index.html.twig', [
            'title' => $this->translator->trans("Newsletters") . " | "
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
     * Newsletter Add Web Page.
     */
    #[Route('/admin/newsletter/add', name: 'app_ui_newsletter_add')]
    public function newsletterAdd(): Response
    {
        $this->logger->info("Render newsletter add page");

        return $this->render('page/newsletter.add.html.twig', [
            'title' => $this->translator->trans("Newsletters") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'templates'      => $this->newsletterModule->getTemplates(),
            'tmp_id'         => Uuid::uuid4()->toString(),
            'app_email'      => $this->configRepository->findValueByName("he_app_email", ""),
            'user'           => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
        ]);
    }

    /**
     * Newsletter Edit Web Page.
     */
    #[Route('/admin/newsletter/edit/{id}', name: 'app_ui_newsletter_edit')]
    public function newsletterEdit(int $id): Response
    {
        $this->logger->info("Render newsletter edit page");

        $newsletter = $this->newsletterModule->findOneById($id);

        if (empty($newsletter)) {
            throw new NotFoundHttpException(sprintf("Newsletter with id %s not found", $id));
        }

        $datetime = $newsletter->getDeliveryTime();
        $date     = (new \DateTimeImmutable())->format("Y-m-d");
        $time     = (new \DateTimeImmutable())->format("H:i");

        if (!empty($datetime)) {
            $date = $datetime->format("Y-m-d");
            $time = $datetime->format("H:i");
        }

        return $this->render('page/newsletter.edit.html.twig', [
            'title' => $this->translator->trans("Newsletters") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'templates'      => $this->newsletterModule->getTemplates(),
            'tmp_id'         => Uuid::uuid4()->toString(),
            'newsletter'     => [
                'id'             => $id,
                'name'           => $newsletter->getName(),
                'email'          => $newsletter->getSender(),
                'deliveryType'   => $newsletter->getDeliveryType(),
                'deliveryStatus' => $newsletter->getDeliveryStatus(),
                'deliveryDate'   => $date,
                'deliveryTime'   => $time,
                'templateName'   => $newsletter->getTemplate(),
                'templateInputs' => $newsletter->getContent(),
            ],
            'user' => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
        ]);
    }

    /**
     * Newsletter View Web Page.
     */
    #[Route('/admin/newsletter/view/{id}', name: 'app_ui_newsletter_view')]
    public function newsletterView(int $id): Response
    {
        $this->logger->info("Render newsletter view page");

        $newsletter = $this->newsletterModule->findOneById($id);

        if (empty($newsletter)) {
            throw new NotFoundHttpException(sprintf("Newsletter with id %s not found", $id));
        }

        $datetime = $newsletter->getDeliveryTime();
        $date     = (new \DateTimeImmutable())->format("Y-m-d");
        $time     = (new \DateTimeImmutable())->format("H:i");

        if (!empty($datetime)) {
            $date = $datetime->format("Y-m-d");
            $time = $datetime->format("H:i");
        }

        return $this->render('page/newsletter.view.html.twig', [
            'title' => $this->translator->trans("Newsletters") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'newsletter'     => [
                'id'             => $id,
                'name'           => $newsletter->getName(),
                'email'          => $newsletter->getSender(),
                'deliveryType'   => $newsletter->getDeliveryType(),
                'deliveryStatus' => $newsletter->getDeliveryStatus(),
                'deliveryDate'   => $date,
                'deliveryTime'   => $time,
                'templateName'   => $newsletter->getTemplate(),
                'templateInputs' => $newsletter->getContent(),
            ],
            'user' => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
        ]);
    }

    /**
     * Newsletter List API Endpoint.
     */
    #[Route('/api/v1/newsletter', name: 'app_endpoint_v1_newsletter_list', methods: ['GET', 'HEAD'])]
    public function newsletterListEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger newsletter list v1 endpoint");

        $limit  = !empty($request->get("limit")) ? (int) ($request->get("limit")) : 20;
        $offset = !empty($request->get("offset")) ? (int) ($request->get("offset")) : 0;

        $result      = [];
        $newsletters = $this->newsletterModule->list($limit, $offset);

        foreach ($newsletters as $newsletter) {
            $outDeliveryStatus = str_replace([
                NewsletterRepository::ON_HOLD_STATUS,
                NewsletterRepository::PENDING_STATUS,
                NewsletterRepository::IN_PROGRESS_STATUS,
                NewsletterRepository::FINISHED_STATUS,
            ], [
                $this->translator->trans("On Hold"),
                $this->translator->trans("Pending"),
                $this->translator->trans("In Progress"),
                $this->translator->trans("Finished"),
            ], $newsletter->getDeliveryStatus());

            $outDeliveryType = str_replace([
                NewsletterRepository::DRAFT_TYPE,
                NewsletterRepository::NOW_TYPE,
                NewsletterRepository::SCHEDULED_TYPE,
            ], [
                $this->translator->trans("Draft"),
                $this->translator->trans("Now"),
                $this->translator->trans("Scheduled"),
            ], $newsletter->getDeliveryType());

            $result[] = [
                'id'             => $newsletter->getId(),
                'name'           => $newsletter->getName(),
                'deliveryStatus' => $outDeliveryStatus,
                'deliveryType'   => $outDeliveryType,
                'createdAt'      => $newsletter->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt'      => $newsletter->getUpdatedAt()->format('Y-m-d H:i:s'),
                'editLink'       => $this->generateUrl('app_ui_newsletter_edit', ['id' => $newsletter->getId()]),
                'viewLink'       => $this->generateUrl('app_ui_newsletter_view', ['id' => $newsletter->getId()]),
            ];
        }

        return $this->json([
            'newsletters' => $result,
            '_metadata'   => [
                'limit'      => $limit,
                'offset'     => $offset,
                'totalCount' => $this->newsletterModule->countAll(),
            ],
        ]);
    }

    /**
     * Newsletter Add API Endpoint.
     */
    #[Route('/api/v1/newsletter', name: 'app_endpoint_v1_newsletter_add', methods: ['POST'])]
    public function newsletterAddEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger newsletter add v1 endpoint");

        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/newsletterAddAction.schema.json"
        );

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('newsletter-update-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        if (NewsletterRepository::SCHEDULED_TYPE === $data->deliveryType) {
            $deliveryTime = new \DateTimeImmutable(sprintf(
                "%s %s",
                $data->deliveryDate,
                $data->deliveryTime
            ));
            $deliveryStatus = NewsletterRepository::ON_HOLD_STATUS;
        } elseif (NewsletterRepository::DRAFT_TYPE === $data->deliveryType) {
            $deliveryTime   = new \DateTimeImmutable();
            $deliveryStatus = NewsletterRepository::ON_HOLD_STATUS;
        } elseif (NewsletterRepository::NOW_TYPE === $data->deliveryType) {
            $deliveryTime   = new \DateTimeImmutable();
            $deliveryStatus = NewsletterRepository::PENDING_STATUS;
        }

        $this->newsletterModule->add([
            'name'           => $data->name,
            'slug'           => $this->newsletterModule->generateSlug($data->name),
            'template'       => $data->templateName,
            'content'        => $data->templateInputs,
            'deliveryStatus' => $deliveryStatus,
            'deliveryType'   => $data->deliveryType,
            'deliveryTime'   => $deliveryTime,
            'sender'         => $data->email,
        ]);

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Newsletter created successfully.'
            ),
        ]);
    }

    /**
     * Newsletter Edit API Endpoint.
     */
    #[Route('/api/v1/newsletter/{id}', name: 'app_endpoint_v1_newsletter_edit', methods: ['PUT'])]
    public function newsletterEditEndpoint(Request $request, int $id): JsonResponse
    {
        $this->logger->info("Trigger newsletter edit v1 endpoint");

        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/newsletterEditAction.schema.json"
        );

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('newsletter-update-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info(sprintf("Update newsletter with id %s", $id));

        if (NewsletterRepository::SCHEDULED_TYPE === $data->deliveryType) {
            $deliveryTime = new \DateTimeImmutable(sprintf(
                "%s %s",
                $data->deliveryDate,
                $data->deliveryTime
            ));
            $deliveryStatus = NewsletterRepository::ON_HOLD_STATUS;
        } elseif (NewsletterRepository::DRAFT_TYPE === $data->deliveryType) {
            $deliveryTime   = new \DateTimeImmutable();
            $deliveryStatus = NewsletterRepository::ON_HOLD_STATUS;
        } elseif (NewsletterRepository::NOW_TYPE === $data->deliveryType) {
            $deliveryTime   = new \DateTimeImmutable();
            $deliveryStatus = NewsletterRepository::PENDING_STATUS;
        }

        $this->newsletterModule->edit($id, [
            'name'           => $data->name,
            'template'       => $data->templateName,
            'content'        => $data->templateInputs,
            'deliveryStatus' => $deliveryStatus,
            'deliveryType'   => $data->deliveryType,
            'deliveryTime'   => $deliveryTime,
            'sender'         => $data->email,
        ]);

        $this->logger->info(sprintf("Newsletter with id %s updated", $id));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Newsletter updated successfully.'
            ),
        ]);
    }

    /**
     * Newsletter Delete API Endpoint.
     */
    #[Route('/api/v1/newsletter/{id}', name: 'app_endpoint_v1_newsletter_delete', methods: ['DELETE'])]
    public function newsletterDeleteEndpoint(Request $request, int $id): JsonResponse
    {
        $this->logger->info("Trigger newsletter delete v1 endpoint");

        $content = $request->getContent();

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('newsletter-delete-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info(sprintf("Delete newsletter with id %s", $id));

        $this->newsletterModule->deleteById($id);

        $this->logger->info(sprintf("Newsletter with id %s deleted", $id));

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Newsletter deleted successfully.'
            ),
        ]);
    }

    /**
     * Newsletter Preview API Endpoint.
     */
    #[Route('/api/v1/newsletter/preview', name: 'app_endpoint_v1_newsletter_preview', methods: ['POST'])]
    public function newsletterPreviewEndpoint(Request $request): JsonResponse
    {
        $this->logger->info("Trigger newsletter add v1 endpoint");

        $content = $request->getContent();

        $this->validator->validate(
            $content,
            "v1/newsletterPreviewAction.schema.json"
        );

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('newsletter-update-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $config = $this->configRepository->findOne($data->tempId);

        $value = json_encode([
            'templateName'   => $data->templateName,
            'templateInputs' => $data->templateInputs,
            'date'           => (new \DateTimeImmutable())->format('Y-m-d'),
            'type'           => 'newsletter_cached_data',
        ]);

        $this->newsletterModule->cleanupCachedData();

        if (empty($config)) {
            $config = Config::fromArray([
                'name'     => $data->tempId,
                'value'    => $value,
                'autoload' => 'off',
            ]);
            $this->configRepository->save($config, true);
        } else {
            $config->setValue($value);
            $this->configRepository->save($config, true);
        }

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Newsletter preview update successfully.'
            ),
        ]);
    }
}
