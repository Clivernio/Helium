<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Weekly project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ResponseSubscriber Class.
 */
class ResponseSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * Class Constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if ('application/json' === $event->getResponse()->headers->get('content-type', '')) {
            $this->logger->info(sprintf(
                'Outgoing %s response, route %s: %s',
                $event->getResponse()->getStatusCode(),
                $event->getRequest()->get('_route'),
                $event->getResponse()->getContent()
            ));
        } else {
            $this->logger->info(sprintf(
                'Outgoing %s response, route %s: <html>...',
                $event->getResponse()->getStatusCode(),
                $event->getRequest()->get('_route')
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
