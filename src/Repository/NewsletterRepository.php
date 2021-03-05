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
     * Count Newsletters.
     *
     * @return int
     */
    public function countAll(): ?int
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
