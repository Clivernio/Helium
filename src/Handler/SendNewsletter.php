<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Handler;

use App\Message\Newsletter as NewsletterMessage;
use App\Repository\ConfigRepository;
use App\Repository\DeliveryRepository;
use App\Service\Mailer;
use App\Service\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;
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

    /** @var DeliveryRepository */
    private $deliveryRepository;

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
        DeliveryRepository $deliveryRepository,
        RouterInterface $router
    ) {
        $this->logger             = $logger;
        $this->worker             = $worker;
        $this->mailer             = $mailer;
        $this->configRepository   = $configRepository;
        $this->translator         = $translator;
        $this->deliveryRepository = $deliveryRepository;
        $this->router             = $router;
    }

    /**
     * Invoke handler.
     */
    public function __invoke(NewsletterMessage $message)
    {
        $data = $message->getContent();

        $this->logger->info(sprintf(
            "Trigger task with UUID %s, and Delivery ID %s",
            $data['task_id'],
            $data['delivery_id']
        ));

        $delivery = $this->deliveryRepository->findOneByID($data['delivery_id']);

        try {
            $subject = $delivery->getNewsletter()->getName() . " - "
            . $this->configRepository->findValueByName("he_app_name", "Helium");

            $content                    = Yaml::parse(trim($delivery->getNewsletter()->getContent()));
            $content["unsubscribe_url"] = rtrim($this->configRepository->findValueByName("he_app_url", ""), "/")
            . $this->router->generate('app_ui_unsubscribe', [
                'email' => $delivery->getSubscriber()->getEmail(),
                'token' => $delivery->getSubscriber()->getToken(),
            ]);

            $content["subject"] = $subject;

            $email_data = [
                'subject'        => $subject,
                'app_name'       => $this->configRepository->findValueByName("he_app_name", ""),
                'app_url'        => $this->configRepository->findValueByName("he_app_url", ""),
                'app_email'      => $this->configRepository->findValueByName("he_app_email", ""),
                'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
                "data"           => $content,
            ];

            $this->mailer->send(
                $delivery->getNewsletter()->getSender(),
                $delivery->getSubscriber()->getEmail(),
                $subject,
                sprintf('newsletter/%s.html.twig', $delivery->getNewsletter()->getTemplate()),
                $email_data
            );
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                "Task with UUID %s, Delivery ID %s failed: %s",
                $data['task_id'],
                $data['delivery_id'],
                $e->getMessage()
            ));

            $this->worker->updateTaskStatus(
                $data['task_id'],
                "failure",
                json_encode(
                    ["errorMessage" => $e->getMessage()]
                )
            );

            $delivery->setStatus(DeliveryRepository::FAILED);
            $this->deliveryRepository->save($delivery, true);

            return;
        }

        $this->logger->info(sprintf(
            "Task with UUID %s, Delivery ID %s succeeded",
            $data['task_id'],
            $data['delivery_id']
        ));

        $delivery->setStatus(DeliveryRepository::SUCCEEDED);
        $this->deliveryRepository->save($delivery, true);
        $this->worker->updateTaskStatus($data['task_id'], "success", json_encode([]));
    }
}
