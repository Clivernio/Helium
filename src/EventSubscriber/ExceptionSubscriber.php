<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\EventSubscriber;

use App\Exception\InvalidRequest;
use App\Exception\ResourceNotFound;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * ExceptionSubscriber Class.
 */
class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /** @var RouterInterface */
    private $router;

    /**
     * Class Constructor.
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, RouterInterface $router)
    {
        $this->logger     = $logger;
        $this->translator = $translator;
        $this->router     = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if ($event->getThrowable() instanceof InvalidRequest) {
            return $this->handleInvalidRequest($event, $event->getThrowable());
        }

        if ($event->getThrowable() instanceof ResourceNotFound) {
            return $this->handleResourceNotFound($event, $event->getThrowable());
        }

        if ($event->getThrowable() instanceof NotFoundHttpException) {
            return $this->handleNotFoundError($event, $event->getThrowable());
        }

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
     * Handle InvalidRequest Exception.
     */
    private function handleInvalidRequest(ExceptionEvent $event, InvalidRequest $e)
    {
        $this->logger->debug(sprintf(
            'InvalidRequest Exception with errorMessage [%s] thrown: %s',
            $e->getMessage(),
            $e->getTraceAsString()
        ));

        $event->setResponse(new JsonResponse([
            'errorCode'    => 'InvalidRequest',
            'errorMessage' => $e->getMessage(),
        ], Response::HTTP_BAD_REQUEST));
    }

    /**
     * Handle ResourceNotFound Exception.
     */
    private function handleResourceNotFound(ExceptionEvent $event, ResourceNotFound $e)
    {
        $this->logger->debug(sprintf(
            'ResourceNotFound Exception with errorMessage [%s] thrown: %s',
            $e->getMessage(),
            $e->getTraceAsString()
        ));

        $event->setResponse(new JsonResponse([
            'errorCode'    => 'ResourceNotFound',
            'errorMessage' => $e->getMessage(),
        ], Response::HTTP_NOT_FOUND));
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
            'errorMessage'  => $this->translator->trans('Internal server error!'),
            'correlationId' => $event->getRequest()->headers->get('X-Correlation-ID', ''),
        ], Response::HTTP_INTERNAL_SERVER_ERROR));
    }

    /**
     * Handle Not Found Error Exception.
     */
    private function handleNotFoundError(ExceptionEvent $event, NotFoundHttpException $e): void
    {
        $this->logger->debug(sprintf(
            'NotFoundHttpException Exception with errorMessage [%s] thrown: %s',
            $e->getMessage(),
            $e->getTraceAsString()
        ));

        $event->setResponse(new RedirectResponse($this->router->generate('app_ui_not_found')));
    }
}
