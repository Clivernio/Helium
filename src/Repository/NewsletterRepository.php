<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Repository;

use App\Entity\Newsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Newsletter Repository.
 *
 * @extends ServiceEntityRepository<Newsletter>
 */
class NewsletterRepository extends ServiceEntityRepository
{
    public const ON_HOLD_STATUS     = "ON_HOLD";
    public const PENDING_STATUS     = "PENDING";
    public const IN_PROGRESS_STATUS = "IN_PROGRESS";
    public const FINISHED_STATUS    = "FINISHED";

    public const DRAFT_TYPE     = "DRAFT";
    public const NOW_TYPE       = "NOW";
    public const SCHEDULED_TYPE = "SCHEDULED";

    /**
     * Class Constructor.
     *
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsletter::class);
    }

    /**
     * Save Entity.
     *
     * @param  Newsletter
     * @param  bool|bool
     */
    public function save(Newsletter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove Entity.
     *
     * @param  Newsletter
     * @param  bool|bool
     */
    public function remove(Newsletter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find a Newsletter By ID.
     */
    public function findOneByID(int $id): ?Newsletter
    {
        $newsletter = $this->findOneBy(['id' => $id]);

        return !empty($newsletter) ? $newsletter : null;
    }

    /**
     * Find a Newsletter By Slug.
     */
    public function findOneBySlug(string $slug): ?Newsletter
    {
        $newsletter = $this->findOneBy(['slug' => $slug]);

        return !empty($newsletter) ? $newsletter : null;
    }

    /**
     * Find Many Newsletters.
     */
    public function findMany(array $order, int $limit, int $offset): array
    {
        return $this->findBy([], $order, $limit, $offset);
    }

    /**
     * Find Sent Out Newsletters.
     */
    public function findSentOut(array $order, int $limit, int $offset): array
    {
        return $this->findBy(['deliveryStatus' => self::FINISHED_STATUS], $order, $limit, $offset);
    }

    /**
     * Count Newsletters.
     *
     * @return int
     */
    public function countAll(string $deliveryStatus = '', string $deliveryType = ''): ?int
    {
        if (!empty($deliveryStatus) && !empty($deliveryType)) {
            return (int) ($this->createQueryBuilder('s')
                ->where('s.deliveryStatus = :deliveryStatus')
                ->andWhere('s.deliveryType = :deliveryType')
                ->setParameter('deliveryStatus', $deliveryStatus)
                ->setParameter('deliveryType', $deliveryType)
                ->select('count(s.id)')
                ->getQuery()
                ->getSingleScalarResult());
        }

        if (!empty($deliveryStatus)) {
            return (int) ($this->createQueryBuilder('s')
                ->where('s.deliveryStatus = :deliveryStatus')
                ->setParameter('deliveryStatus', $deliveryStatus)
                ->select('count(s.id)')
                ->getQuery()
                ->getSingleScalarResult());
        }

        if (!empty($deliveryType)) {
            return (int) ($this->createQueryBuilder('s')
                ->where('s.deliveryType = :deliveryType')
                ->setParameter('deliveryType', $deliveryType)
                ->select('count(s.id)')
                ->getQuery()
                ->getSingleScalarResult());
        }

        return (int) ($this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult());
    }

    /**
     * Get Newsletters Sent Out Over Time.
     */
    public function getNewslettersSentOutOverTime(int $days = 7): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = sprintf("SELECT COUNT(*) AS count, DATE(created_at) AS date
            FROM he_newsletter n WHERE n.created_at >= DATE_SUB(curdate(), INTERVAL %d DAY)
            AND n.delivery_status = :deliveryStatus
            GROUP BY date", $days);

        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['deliveryStatus' => self::FINISHED_STATUS]);

        return $resultSet->fetchAllAssociative();
    }

    /**
     * Get Pending Newsletters.
     */
    public function getPendingNewsletters(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = sprintf(
            "SELECT * FROM he_newsletter n
            WHERE (n.delivery_type = '%s' AND n.delivery_status = '%s')
            OR (n.delivery_status = '%s')
            OR (n.delivery_type = '%s' AND n.delivery_status = '%s' AND n.delivery_time <= :timeNow)",
            self::NOW_TYPE,
            self::PENDING_STATUS,
            self::IN_PROGRESS_STATUS,
            self::SCHEDULED_TYPE,
            self::ON_HOLD_STATUS
        );

        $stmt      = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['timeNow' => (new \DateTime())->format("Y-m-d H:i:s")]);

        return $resultSet->fetchAllAssociative();
    }
}
