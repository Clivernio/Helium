<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Repository;

use App\Entity\NewsletterMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * NewsletterMeta Repository.
 *
 * @extends ServiceEntityRepository<NewsletterMeta>
 */
class NewsletterMetaRepository extends ServiceEntityRepository
{
    /**
     * Class Constructor.
     *
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterMeta::class);
    }

    /**
     * Save Entity.
     *
     * @param  NewsletterMeta
     * @param  bool|bool
     */
    public function save(NewsletterMeta $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove Entity.
     *
     * @param  NewsletterMeta
     * @param  bool|bool
     */
    public function remove(NewsletterMeta $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
