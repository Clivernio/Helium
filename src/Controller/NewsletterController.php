<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

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

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
    }

    /**
     * Newsletter Web Page.
     */
    #[Route('/admin/newsletter', name: 'app_ui_newsletters_index')]
    public function newsletter(): Response
    {
        $this->logger->info("Render newsletter page");

        return $this->render('page/newsletters_index.html.twig', [
            'title' => $this->translator->trans("Newsletters") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
        ]);
    }
}
