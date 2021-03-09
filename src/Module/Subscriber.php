<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Entity\Subscriber as SubscriberEntity;
use App\Exception\InvalidRequest;
use App\Exception\ResourceNotFound;
use App\Repository\ConfigRepository;
use App\Repository\SubscriberRepository;
use Psr\Log\LoggerInterface;

/**
 * Subscriber Module.
 */
class Subscriber
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var SubscriberRepository */
    private $subscriberRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        SubscriberRepository $subscriberRepository
    ) {
        $this->logger               = $logger;
        $this->configRepository     = $configRepository;
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * Count Subscribers By Status.
     */
    public function countByStatus(string $status): ?int
    {
        return $this->subscriberRepository->countByStatus($status);
    }

    /**
     * Add a Subscriber.
     */
    public function add(array $data): ?SubscriberEntity
    {
        if (!empty($this->subscriberRepository->findOneByEmail($data['email']))) {
            throw new InvalidRequest(sprintf("Email %s is already used", $data['email']));
        }

        $subscriber = SubscriberEntity::fromArray([
            "email"  => $data["email"],
            "status" => $data["status"],
            "token"  => bin2hex(random_bytes(rand(30, 40))),
        ]);

        $this->subscriberRepository->save($subscriber, true);

        return $subscriber;
    }

    /**
     * Edit a Subscriber.
     */
    public function edit(int $id, array $data): ?SubscriberEntity
    {
        $subscriber = $this->subscriberRepository->findOneByID($id);

        if (empty($subscriber)) {
            throw new ResourceNotFound(sprintf("Subscriber with id %s not found", $id));
        }

        if (!empty($data['email']) && ($data['email'] !== $subscriber->email)) {
            if (empty($this->subscriberRepository->findOneByEmail($data['email']))) {
                // Override Email
                $subscriber->email = $data['email'];
            } else {
                // Throw invalid request error
                throw new InvalidRequest(sprintf("Email %s is already used", $data['email']));
            }
        }

        if (!empty($data['status'])) {
            $subscriber->status = $data['status'];
        }

        if (!empty($data['token'])) {
            $subscriber->token = $data['token'];
        }

        $this->subscriberRepository->save($subscriber, true);

        return $subscriber;
    }

    /**
     * List Subscribers.
     */
    public function list(string $status, int $limit = 20, int $offset = 0): array
    {
        return $this->subscriberRepository->findManyByStatus(
            $status,
            ['createdAt' => 'DESC'],
            $limit,
            $offset
        );
    }

    /**
     * Delete Subscriber.
     */
    public function delete(int $id): void
    {
        $subscriber = $this->subscriberRepository->findOneByID($id);

        if (empty($subscriber)) {
            throw new ResourceNotFound(sprintf("Subscriber with id %s not found", $id));
        }

        $this->subscriberRepository->remove($subscriber, true);
    }

    /**
     * Find One By ID.
     *
     * @return SubscriberEntity
     */
    public function findOneById(int $id): ?SubscriberEntity
    {
        return $this->subscriberRepository->findOneByID($id);
    }

    /**
     * Find One By Email.
     *
     * @return SubscriberEntity
     */
    public function findOneByEmail(string $email): ?SubscriberEntity
    {
        return $this->subscriberRepository->findOneByEmail($email);
    }
}
