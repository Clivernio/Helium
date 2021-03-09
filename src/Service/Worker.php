<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Service;

use App\Entity\Task;
use App\Message\Ping;
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
     * Ping Task.
     */
    public function ping(string $message = "pong")
    {
        $uuid = Uuid::uuid4()->toString();

        $data = [
            'task_id' => $uuid,
            'message' => $message,
        ];

        $this->storeTask($uuid, json_encode($data));
        $this->messageBus->dispatch(new Ping($data));
    }

    /**
     * Store a Task.
     */
    public function storeTask(string $uuid, string $payload)
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
