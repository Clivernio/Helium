<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Repository;

use App\Entity\SubscriberMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * SubscriberMeta Repository.
 *
 * @extends ServiceEntityRepository<SubscriberMeta>
 */
class SubscriberMetaRepository extends ServiceEntityRepository
{
    /**
     * Class Constructor.
     *
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriberMeta::class);
    }

    /**
     * Save Entity.
     *
     * @param  SubscriberMeta
     * @param  bool|bool
     */
    public function save(SubscriberMeta $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove Entity.
     *
     * @param  SubscriberMeta
     * @param  bool|bool
     */
    public function remove(SubscriberMeta $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
