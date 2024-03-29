<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Repository;

use App\Entity\UserMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * UserMeta Repository.
 *
 * @extends ServiceEntityRepository<UserMeta>
 */
class UserMetaRepository extends ServiceEntityRepository
{
    /**
     * Class Constructor.
     *
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMeta::class);
    }

    /**
     * Save Entity.
     *
     * @param  UserMeta
     * @param  bool|bool
     */
    public function save(UserMeta $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove Entity.
     *
     * @param  UserMeta
     * @param  bool|bool
     */
    public function remove(UserMeta $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find a Meta by Value.
     */
    public function findMetaByValue(string $value): ?UserMeta
    {
        $meta = $this->findOneBy(['value' => $value]);

        return !empty($meta) ? $meta : null;
    }

    /**
     * Filter Meta.
     *
     * @return UserMeta
     */
    public function filterMeta(array $filter = []): ?UserMeta
    {
        $meta = $this->findOneBy($filter);

        return !empty($meta) ? $meta : null;
    }
}
