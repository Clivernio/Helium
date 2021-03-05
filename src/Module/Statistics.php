<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Repository\ConfigRepository;
use App\Repository\NewsletterRepository;
use App\Repository\SubscriberRepository;
use Psr\Log\LoggerInterface;

/**
 * Statistics Module.
 */
class Statistics
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var SubscriberRepository */
    private $subscriberRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        SubscriberRepository $subscriberRepository,
        NewsletterRepository $newsletterRepository
    ) {
        $this->logger               = $logger;
        $this->configRepository     = $configRepository;
        $this->subscriberRepository = $subscriberRepository;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * Get Total Subscribers.
     */
    public function getTotalSubscribers(): int
    {
        return $this->subscriberRepository->countByStatus();
    }

    /**
     * Get Total Active Subscribers.
     */
    public function getTotalActiveSubscribers(): int
    {
        return $this->subscriberRepository->countByStatus(SubscriberRepository::SUBSCRIBED);
    }

    /**
     * Get Total Non Active Subscribers.
     */
    public function getTotalNonActiveSubscribers(): int
    {
        return $this->subscriberRepository->countByStatus(SubscriberRepository::UNSUBSCRIBED);
    }

    /**
     * Get Total Unverified Subscribers.
     */
    public function getTotalUnverifiedSubscribers(): int
    {
        return $this->subscriberRepository->countByStatus(SubscriberRepository::PENDING_VERIFY);
    }

    /**
     * Get Total Draft Newsletters.
     */
    public function getTotalDraftNewsletters(): int
    {
        return $this->newsletterRepository->countAll('', NewsletterRepository::DRAFT_TYPE);
    }

    /**
     * Get Total Scheduled Newsletters.
     */
    public function getTotalScheduledNewsletters(): int
    {
        return $this->newsletterRepository->countAll('', NewsletterRepository::SCHEDULED_TYPE);
    }

    /**
     * Get Total On Hold Newsletters.
     */
    public function getTotalOnHoldNewsletters(): int
    {
        return $this->newsletterRepository->countAll(NewsletterRepository::ON_HOLD_STATUS, '');
    }

    /**
     * Get Total Pending Newsletters.
     */
    public function getTotalPendingNewsletters(): int
    {
        return $this->newsletterRepository->countAll(NewsletterRepository::PENDING_STATUS, '');
    }

    /**
     * Get Total In Progress Newsletters.
     */
    public function getTotalInProgressNewsletters(): int
    {
        return $this->newsletterRepository->countAll(NewsletterRepository::IN_PROGRESS_STATUS, '');
    }

    /**
     * Get Total Sent Out Newsletters.
     */
    public function getTotalSentOutNewsletters(): int
    {
        return $this->newsletterRepository->countAll(NewsletterRepository::FINISHED_STATUS, '');
    }

    /**
     * Get Newsletter Sending Out Progress. This Measured for Each Newsletter.
     */
    public function getNewsletterSendingOutProgress(int $newsletterId): string
    {
        return '';
    }

    /**
     * Get Subscribers Over Time.
     */
    public function getSubscribersOverTime(int $days = 7): array
    {
        return $this->subscriberRepository->getSubscriberOverTime($days, "");
    }

    /**
     * Get Active Subscribers Over Time.
     */
    public function getActiveSubscribersOverTime(int $days = 7): array
    {
        return $this->subscriberRepository->getSubscriberOverTime($days, SubscriberRepository::SUBSCRIBED);
    }

    /**
     * Get Non Active Subscribers Over Time.
     */
    public function getNonActiveSubscribersOverTime(int $days = 7): array
    {
        return $this->subscriberRepository->getSubscriberOverTime($days, SubscriberRepository::UNSUBSCRIBED);
    }

    /**
     * Get Non Active Subscribers Over Time.
     */
    public function getPendingSubscribersOverTime(int $days = 7): array
    {
        return $this->subscriberRepository->getSubscriberOverTime($days, SubscriberRepository::PENDING_VERIFY);
    }

    /**
     * Get Newsletters Sent Out Over Time.
     */
    public function getNewslettersSentOutOverTime(int $days = 7): array
    {
        return $this->newsletterRepository->getNewslettersSentOutOverTime($days);
    }
}
