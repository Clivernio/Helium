<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Repository;

use App\Entity\Config;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Config Repository.
 *
 * @extends ServiceEntityRepository<Config>
 */
class ConfigRepository extends ServiceEntityRepository
{
    /**
     * Class Constructor.
     *
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    /**
     * Save Entity.
     *
     * @param  Config
     * @param  bool|bool
     */
    public function save(Config $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove Entity.
     *
     * @param  Config
     * @param  bool|bool
     */
    public function remove(Config $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find an Config by a Key.
     */
    public function findOne(string $name): ?Config
    {
        $config = $this->findOneBy(['name' => $name]);

        return !empty($config) ? $config : null;
    }

    /**
     * Find Value by A Key.
     *
     * @return string
     */
    public function findValueByName(string $name, string $default): ?string
    {
        $config = $this->findOne($name);

        return !empty($config) ? $config->getValue() : $default;
    }

    /**
     * Find options list by keys.
     */
    public function findMany(array $keys): array
    {
        return [];
    }
}
