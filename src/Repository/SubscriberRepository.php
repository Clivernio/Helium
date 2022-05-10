<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Repository;

use App\Entity\Subscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Subscriber Repository.
 *
 * @extends ServiceEntityRepository<Subscriber>
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public const SUBSCRIBED   = "SUBSCRIBED";
    public const UNSUBSCRIBED = "UNSUBSCRIBED";
    public const TRASHED      = "TRASHED";
    public const REMOVED      = "REMOVED";

    /**
     * Class Constructor.
     *
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    /**
     * Save Entity.
     *
     * @param  Subscriber
     * @param  bool|bool
     */
    public function save(Subscriber $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove Entity.
     *
     * @param  Subscriber
     * @param  bool|bool
     */
    public function remove(Subscriber $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find a Subscriber By ID.
     */
    public function findOneByID(int $id): ?Subscriber
    {
        $subscriber = $this->findOneBy(['id' => $id]);

        return !empty($subscriber) ? $subscriber : null;
    }

    /**
     * Find a Subscriber By Email.
     */
    public function findOneByEmail(string $email): ?Subscriber
    {
        $subscriber = $this->findOneBy(['email' => $email]);

        return !empty($subscriber) ? $subscriber : null;
    }

    /**
     * Count Subscribers.
     *
     * @return int
     */
    public function countByStatus(string $status): ?int
    {
        if (empty($status)) {
            return $this->createQueryBuilder('s')
                ->select('count(s.id)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $this->createQueryBuilder('s')
            ->where(sprintf('s.status = %s', $status))
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find Many By Status.
     */
    public function findManyByStatus(string $status, array $order, int $limit, int $offset): array
    {
        return $this->findBy(['status' => $status], $order, $limit, $offset);
    }
}
