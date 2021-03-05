<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Statistics as StatisticsModule;
use App\Repository\ConfigRepository;
use App\Repository\SubscriberRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Dashboard Controller.
 */
class DashboardController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var StatisticsModule */
    private $statisticsModule;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        StatisticsModule $statisticsModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->statisticsModule = $statisticsModule;
    }

    /**
     * Dashboard Web Page.
     */
    #[Route('/admin/dashboard', name: 'app_ui_dashboard')]
    public function dashboard(): Response
    {
        $this->logger->info("Render dashboard page");

        return $this->render('page/dashboard.html.twig', [
            'title' => $this->translator->trans("Dashboard") . " | "
            . $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'user'           => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
            ],
            'statistics' => [
                'total_subscribers'                => $this->statisticsModule->getTotalSubscribers(),
                'total_active_subscribers'         => $this->statisticsModule->getTotalActiveSubscribers(),
                'total_nonactive_subscribers'      => $this->statisticsModule->getTotalNonActiveSubscribers(),
                'total_unverified_subscribers'     => $this->statisticsModule->getTotalUnverifiedSubscribers(),
                'total_draft_newsletters'          => $this->statisticsModule->getTotalDraftNewsletters(),
                'total_scheduled_newsletters'      => $this->statisticsModule->getTotalScheduledNewsletters(),
                'total_onhold_newsletters'         => $this->statisticsModule->getTotalOnHoldNewsletters(),
                'total_pending_newsletters'        => $this->statisticsModule->getTotalPendingNewsletters(),
                'total_inprogress_newsletters'     => $this->statisticsModule->getTotalInProgressNewsletters(),
                'total_sentout_newsletters'        => $this->statisticsModule->getTotalSentOutNewsletters(),
                'subscribers_over_time'            => $this->statisticsModule->getSubscribersOverTime(),
                'active_subscribers_over_time'     => $this->statisticsModule->getActiveSubscribersOverTime(),
                'non_active_subscribers_over_time' => $this->statisticsModule->getNonActiveSubscribersOverTime(),
                'pending_subscribers_over_time'    => $this->statisticsModule->getPendingSubscribersOverTime(),
                'newsletters_sentout_over_time'    => $this->statisticsModule->getNewslettersSentOutOverTime(),
                'latest_subscribers'               => $this->statisticsModule->getLatestSubscribers(
                    "",
                    5
                ),
                'latest_active_subscribers' => $this->statisticsModule->getLatestSubscribers(
                    SubscriberRepository::SUBSCRIBED,
                    5
                ),
                'latest_non_active_subscribers' => $this->statisticsModule->getLatestSubscribers(
                    SubscriberRepository::UNSUBSCRIBED,
                    5
                ),
                'latest_unverified_subscribers' => $this->statisticsModule->getLatestSubscribers(
                    SubscriberRepository::PENDING_VERIFY,
                    5
                ),
                'latest_newsletters_sent_out' => $this->statisticsModule->getLatestSentOutNewsletters(),
            ],
        ]);
    }
}
