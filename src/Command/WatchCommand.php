<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Command;

use App\Entity\Delivery as DeliveryEntity;
use App\Message\Newsletter;
use App\Repository\ConfigRepository;
use App\Repository\DeliveryRepository;
use App\Repository\NewsletterRepository;
use App\Repository\SubscriberRepository;
use App\Repository\TaskRepository;
use App\Service\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Watch Command.
 */
class WatchCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'watch';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Watch new newsletters';

    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var DeliveryRepository */
    private $deliveryRepository;

    /** @var SubscriberRepository */
    private $subscriberRepository;

    /** @var NewsletterRepository */
    private $newsletterRepository;

    /** @var TaskRepository */
    private $taskRepository;

    /** @var Worker */
    private $worker;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        DeliveryRepository $deliveryRepository,
        SubscriberRepository $subscriberRepository,
        NewsletterRepository $newsletterRepository,
        TaskRepository $taskRepository,
        Worker $worker
    ) {
        $this->logger               = $logger;
        $this->configRepository     = $configRepository;
        $this->deliveryRepository   = $deliveryRepository;
        $this->subscriberRepository = $subscriberRepository;
        $this->newsletterRepository = $newsletterRepository;
        $this->taskRepository       = $taskRepository;
        $this->worker               = $worker;
        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure(): void
    {
        // ...
    }

    /**
     * Execute command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('Worker started!');

        while (true) {
            $newsletters = $this->newsletterRepository->getPendingNewsletters();

            $limit = 100;

            foreach ($newsletters as $newsletter) {
                $offset = 0;

                // Update newsletter to be in progress
                $newsletterObj = $this->newsletterRepository->findOneByID($newsletter['id']);
                $newsletterObj->setDeliveryStatus(NewsletterRepository::IN_PROGRESS_STATUS);
                $this->newsletterRepository->save($newsletterObj, true);

                while (true) {
                    $subscribers = $this->subscriberRepository->findManyByStatus(
                        SubscriberRepository::SUBSCRIBED,
                        ['createdAt' => 'ASC'],
                        $limit,
                        $offset
                    );

                    if (0 === count($subscribers)) {
                        break;
                    }

                    foreach ($subscribers as $subscriber) {
                        $item = $this->deliveryRepository->findByFilter([
                            'subscriber' => $subscriber->getId(),
                            'newsletter' => $newsletter['id'],
                        ]);

                        if (!empty($item)) {
                            continue;
                        }

                        $delivery = $this->createDelivery(
                            $newsletter['id'],
                            $subscriber->getId()
                        );

                        $this->worker->dispatch(
                            new Newsletter(),
                            [
                                'delivery_id' => $delivery->getId(),
                            ]
                        );
                    }

                    $offset += $limit;
                }

                // Update newsletter to be finished
                $newsletterObj = $this->newsletterRepository->findOneByID($newsletter['id']);
                $newsletterObj->setDeliveryStatus(NewsletterRepository::FINISHED_STATUS);
                $this->newsletterRepository->save($newsletterObj, true);
            }

            sleep(10);
        }

        return Command::SUCCESS;
    }

    /**
     * Create a Newsletter Delivery.
     */
    private function createDelivery(int $newsletterId, int $subscriberId): DeliveryEntity
    {
        $delivery = DeliveryEntity::fromArray([
            'status'     => DeliveryRepository::IN_PROGRESS,
            'subscriber' => $this->subscriberRepository->findOneByID($subscriberId),
            'newsletter' => $this->newsletterRepository->findOneByID($newsletterId),
        ]);

        $this->deliveryRepository->save($delivery, true);

        return $delivery;
    }
}
