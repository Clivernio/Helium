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
 * Not Found Controller.
 */
class NotFoundController extends AbstractController
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
     * Not Found Web Page.
     */
    #[Route('/404', name: 'app_ui_not_found')]
    public function notFound(): Response
    {
        $this->logger->info("Render not found page");

        return $this->render('page/not_found.html.twig', [
            'title' => $this->translator->trans("404") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
        ]);
    }
}
