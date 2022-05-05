<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Handler;

use App\Message\Newsletter as NewsletterMessage;
use App\Repository\ConfigRepository;
use App\Service\Mailer;
use App\Service\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Newsletter Message Handler.
 */
#[AsMessageHandler]
class SendNewsletter
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Worker */
    private $worker;

    /** @var Mailer */
    private $mailer;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        Worker $worker,
        Mailer $mailer,
        ConfigRepository $configRepository,
        TranslatorInterface $translator
    ) {
        $this->logger           = $logger;
        $this->worker           = $worker;
        $this->mailer           = $mailer;
        $this->configRepository = $configRepository;
        $this->translator       = $translator;
    }

    /**
     * Invoke handler.
     */
    public function __invoke(NewsletterMessage $message)
    {
        $data = $message->getContent();

        $this->logger->info(sprintf(
            "Trigger task with UUID %s, and delivery id %s",
            $data['task_id'],
            $data['delivery_id']
        ));
    }
}
