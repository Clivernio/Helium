<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\EventSubscriber;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ExceptionSubscriber Class.
 */
class ExceptionSubscriber implements EventSubscriberInterface
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
    public function onKernelException(ExceptionEvent $event)
    {
        return $this->handleUnexpectedError($event, $event->getThrowable());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * Handle Unexpected Exception.
     *
     * @param Exception $e
     */
    private function handleUnexpectedError(ExceptionEvent $event, $e): void
    {
        $this->logger->error(sprintf(
            'Exception with errorMessage %s httpCode %s thrown: %s',
            $e->getMessage(),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $e->getTraceAsString()
        ));

        $event->setResponse(new JsonResponse([
            'errorMessage'  => $e->getMessage(),
            'correlationId' => $event->getRequest()->headers->get('X-Correlation-ID', ''),
        ], Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
