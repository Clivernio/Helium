<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Repository\OptionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Error Controller.
 */
class ErrorController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var OptionRepository */
    private $optionRepository;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        OptionRepository $optionRepository,
        TranslatorInterface $translator
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->optionRepository = $optionRepository;
    }

    /**
     * Error Web Page.
     */
    #[Route('/500', name: 'app_error_web')]
    public function error(): Response
    {
        $this->logger->info("Render error page");

        return $this->render('page/error.html.twig', [
            'title' => $this->translator->trans("500") . " | "
            . $this->optionRepository->findValueByKey("mw_app_name", "Midway"),
        ]);
    }
}
