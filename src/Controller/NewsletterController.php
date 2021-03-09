<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Newsletter as NewsletterModule;
use App\Repository\ConfigRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        NewsletterModule $newsletterModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->newsletterModule = $newsletterModule;
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
    public function newsletterEdit(): Response
    {
        $this->logger->info("Render newsletter edit page");

        return $this->render('page/newsletter.edit.html.twig', [
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
     * Newsletter View Web Page.
     */
    #[Route('/admin/newsletter/view/{id}', name: 'app_ui_newsletter_view')]
    public function newsletterView(): Response
    {
        $this->logger->info("Render newsletter view page");

        return $this->render('page/newsletter.view.html.twig', [
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

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('newsletter-add-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        // ...

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

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('newsletter-edit-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->logger->info(sprintf("Update newsletter with id %s", $id));

        // ..

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
}
