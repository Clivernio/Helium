<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\EventSubscriber;

use App\Exception\InvalidRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * DemoSubscriber Class.
 */
class DemoSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var array */
    private $disabled = [
        'app_endpoint_v1_install',
        'app_endpoint_v1_forgot_password',
        'app_endpoint_v1_reset_password',
        'app_endpoint_v1_profile',
        'app_endpoint_v1_settings',
        'app_endpoint_v1_newsletter_delete',
        'app_endpoint_v1_newsletter_edit',
        'app_endpoint_v1_newsletter_add',
        'app_endpoint_v1_subscriber_add',
        'app_endpoint_v1_subscriber_edit',
        'app_endpoint_v1_subscribe',
        'app_endpoint_v1_unsubscribe',
    ];

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!isset($_ENV['DEMO_MODE'])) {
            return;
        }

        $this->logger->info(sprintf(
            'Trigger demo middleware for %s request, route %s and uri %s',
            $event->getRequest()->getMethod(),
            $event->getRequest()->get('_route'),
            $event->getRequest()->getUri()
        ));

        if (in_array(strtolower($event->getRequest()->get('_route')), $this->disabled, true)) {
            throw new InvalidRequest("Sorry! this feature is disabled on demo mode.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
