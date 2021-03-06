<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Handler;

use App\Message\VerifyEmail as VerifyEmailMessage;
use App\Repository\ConfigRepository;
use App\Service\Mailer;
use App\Service\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Verify Email Message Handler.
 */
#[AsMessageHandler]
class VerifyEmail
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

    /** @var RouterInterface */
    private $router;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        Worker $worker,
        Mailer $mailer,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->logger           = $logger;
        $this->worker           = $worker;
        $this->mailer           = $mailer;
        $this->configRepository = $configRepository;
        $this->translator       = $translator;
        $this->router           = $router;
    }

    /**
     * Invoke handler.
     */
    public function __invoke(VerifyEmailMessage $message)
    {
        $data = $message->getContent();

        $this->logger->info(sprintf(
            "Trigger task with UUID %s, email %s and token %s",
            $data['task_id'],
            $data['email'],
            $data['token']
        ));

        try {
            $subject = $this->translator->trans("Verify Email") . " - "
            . $this->configRepository->findValueByName("he_app_name", "Helium");

            $from = $this->configRepository->findValueByName("he_app_email", "no_reply@example.com");

            $verify_url = rtrim($this->configRepository->findValueByName("he_app_url", ""), "/")
            . $this->router->generate('app_ui_verify_subscriber', [
                'email' => $data['email'],
                'token' => $data['token'],
            ]);

            $email_data = [
                "subject"       => $subject,
                "app_name"      => $this->configRepository->findValueByName("he_app_name", "Helium"),
                "app_url"       => $this->configRepository->findValueByName("he_app_url"),
                "token"         => $data['token'],
                "email_address" => $data['email'],
                "verify_url"    => $verify_url,
            ];

            $this->mailer->send(
                $from,
                $data['email'],
                $subject,
                "mail/verify-email.html.twig",
                $email_data
            );
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                "Task with UUID %s, email %s and token %s failed: %s",
                $data['task_id'],
                $data['email'],
                $data['token'],
                $e->getMessage()
            ));

            $this->worker->updateTaskStatus(
                $data['task_id'],
                "failure",
                json_encode(
                    ["errorMessage" => $e->getMessage()]
                )
            );

            return;
        }

        $this->logger->info(sprintf(
            "Task with UUID %s, email %s and token %s succeeded",
            $data['task_id'],
            $data['email'],
            $data['token']
        ));

        $this->worker->updateTaskStatus($data['task_id'], "success", json_encode([]));
    }
}
