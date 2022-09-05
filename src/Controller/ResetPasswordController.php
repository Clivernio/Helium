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
 * Reset Password Controller.
 */
class ResetPasswordController extends AbstractController
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
     * Reset Password Web Page.
     */
    #[Route('/reset-password/{token}', name: 'app_reset_password_web')]
    public function fpwd(): Response
    {
        $this->logger->info("Render reset password page");

        return $this->render('page/reset_password.html.twig', [
            'title' => $this->optionRepository->findValueByKey("mw_app_name", "Midway"),
        ]);
    }
}
