<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Repository;

use App\Entity\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Delivery Repository.
 *
 * @extends ServiceEntityRepository<Delivery>
 */
class DeliveryRepository extends ServiceEntityRepository
{
    public const IN_PROGRESS = "IN_PROGRESS";
    public const FAILED      = "FAILED";
    public const SUCCEEDED   = "SUCCEEDED";

    /**
     * Class Constructor.
     *
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    /**
     * Save Entity.
     *
     * @param  Delivery
     * @param  bool|bool
     */
    public function save(Delivery $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove Entity.
     *
     * @param  Delivery
     * @param  bool|bool
     */
    public function remove(Delivery $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find a Delivery By ID.
     */
    public function findOneByID(int $id): ?Delivery
    {
        $delivery = $this->findOneBy(['id' => $id]);

        return !empty($delivery) ? $delivery : null;
    }

    /**
     * Find by a Filter.
     */
    public function findByFilter(array $filter): ?Delivery
    {
        $delivery = $this->findOneBy($filter);

        return !empty($delivery) ? $delivery : null;
    }
}
