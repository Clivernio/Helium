<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Module;

use App\Entity\Newsletter as NewsletterEntity;
use App\Exception\ResourceNotFound;
use App\Repository\ConfigRepository;
use App\Repository\NewsletterRepository;
use Psr\Log\LoggerInterface;

/**
 * Newsletter Module.
 */
class Newsletter
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var NewsletterRepository */
    private $newsletterRepository;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        NewsletterRepository $newsletterRepository
    ) {
        $this->logger               = $logger;
        $this->configRepository     = $configRepository;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * Delete a Newsletter by ID.
     */
    public function deleteById(int $id): void
    {
        $newsletter = $this->newsletterRepository->findOneByID($id);

        if (empty($newsletter)) {
            throw new ResourceNotFound(sprintf("Newsletter with id %s not found", $id));
        }

        $this->newsletterRepository->remove($newsletter, true);
    }

    /**
     * Add a Newsletter.
     *
     * @return NewsletterEntity
     */
    public function add(array $data): ?NewsletterEntity
    {
        $newsletter = NewsletterEntity::fromArray([
            'name'           => $data['name'],
            'deliveryStatus' => $data['deliveryStatus'],
            'deliveryType'   => $data['deliveryType'],
            'deliveryTime'   => $data['deliveryTime'],
        ]);

        $this->newsletterRepository->save($newsletter, true);

        return $newsletter;
    }

    /**
     * Edit a Newsletter.
     *
     * @return NewsletterEntity
     */
    public function edit(int $id, array $data): ?NewsletterEntity
    {
        $newsletter = $this->newsletterRepository->findOneByID($id);

        if (empty($newsletter)) {
            throw new ResourceNotFound(sprintf("Newsletter with id %s not found", $id));
        }

        $newsletter->setName($data['name']);
        $newsletter->setDeliveryStatus($data['deliveryStatus']);
        $newsletter->setDeliveryType($data['deliveryType']);
        $newsletter->setDeliveryTime($data['deliveryTime']);

        $this->newsletterRepository->save($newsletter, true);

        return $newsletter;
    }
}
