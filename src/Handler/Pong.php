<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Handler;

use App\Message\Ping as PingMessage;
use App\Service\Worker;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Ping Message Handler.
 */
#[AsMessageHandler]
class Pong
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Worker */
    private $worker;

    /**
     * Class Constructor.
     */
    public function __construct(LoggerInterface $logger, Worker $worker)
    {
        $this->logger = $logger;
        $this->worker = $worker;
    }

    /**
     * Invoke handler.
     */
    public function __invoke(PingMessage $message)
    {
        $data = $message->getContent();

        $this->logger->info(sprintf(
            "Trigger task with UUID %s and message %s",
            $data['task_id'],
            $data['message']
        ));

        $this->worker->updateTaskStatus($data['task_id'], "success", "");
    }
}
