<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Service;

use App\Entity\Task;
use App\Message\MessageInterface;
use App\Repository\TaskRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Worker Service.
 */
class Worker
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        MessageBusInterface $messageBus,
        TaskRepository $taskRepository
    ) {
        $this->messageBus     = $messageBus;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Dispatch a Task.
     */
    public function dispatch(MessageInterface $message, array $data = []): string
    {
        $uuid    = Uuid::uuid4()->toString();
        $content = array_merge(['task_id' => $uuid], $data);

        $this->storeTask($uuid, json_encode($content));
        $message->setContent($content);
        $this->messageBus->dispatch($message);

        return $uuid;
    }

    /**
     * Store a Task.
     */
    private function storeTask(string $uuid, string $payload)
    {
        $task = Task::fromArray([
            'status'  => 'PENDING',
            'uuid'    => $uuid,
            'payload' => $payload,
            'result'  => '',
        ]);

        $this->taskRepository->save($task, true);
    }

    /**
     * Update Task Status.
     */
    public function updateTaskStatus(string $uuid, string $status, string $result): bool
    {
        $task = $this->taskRepository->findOneByUuid($uuid);

        if (!$task) {
            return false;
        }

        $task->setStatus(strtoupper($status));
        $task->setResult($result);
        $this->taskRepository->save($task, true);

        return true;
    }
}
