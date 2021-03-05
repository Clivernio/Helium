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
use Symfony\Component\HttpKernel\KernelInterface;

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

    /** KernelInterface $appKernel */
    private $appKernel;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        NewsletterRepository $newsletterRepository,
        KernelInterface $appKernel
    ) {
        $this->logger               = $logger;
        $this->configRepository     = $configRepository;
        $this->newsletterRepository = $newsletterRepository;
        $this->appKernel            = $appKernel;
    }

    /**
     * Find a Newsletter by ID.
     */
    public function findOneById(int $id): NewsletterEntity
    {
        return $this->newsletterRepository->findOneByID($id);
    }

    /**
     * Delete a Newsletter by ID.
     */
    public function deleteOneById(int $id): void
    {
        $newsletter = $this->findOneById($id);

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
            'template'       => $data['template'],
            'content'        => $data['content'],
            'deliveryStatus' => $data['deliveryStatus'],
            'deliveryType'   => $data['deliveryType'],
            'deliveryTime'   => $data['deliveryTime'],
            'sender'         => $data['sender'],
            'slug'           => $data['slug'],
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

        if (!empty($data['name'])) {
            $newsletter->setName($data['name']);
        }

        if (!empty($data['template'])) {
            $newsletter->setTemplate($data['template']);
        }

        if (!empty($data['content'])) {
            $newsletter->setContent($data['content']);
        }

        if (!empty($data['sender'])) {
            $newsletter->setSender($data['sender']);
        }

        if (!empty($data['slug'])) {
            $newsletter->setSlug($data['slug']);
        }

        if (!empty($data['deliveryStatus'])) {
            $newsletter->setDeliveryStatus($data['deliveryStatus']);
        }

        if (!empty($data['deliveryType'])) {
            $newsletter->setDeliveryType($data['deliveryType']);
        }

        if (!empty($data['deliveryTime'])) {
            $newsletter->setDeliveryTime($data['deliveryTime']);
        }

        if (null === $data['deliveryTime']) {
            $newsletter->setDeliveryTime(nulll);
        }

        $this->newsletterRepository->save($newsletter, true);

        return $newsletter;
    }

    /**
     * Get Templates.
     */
    public function getTemplates(): array
    {
        $result       = [];
        $basePath     = rtrim($this->appKernel->getProjectDir(), "/");
        $templatePath = sprintf("%s/templates/default/newsletter", $basePath);
        $templates    = scandir($templatePath);

        foreach ($templates as $template) {
            if (false !== strpos($template, ".html.twig")) {
                $defaults = "";
                $name     = str_replace(".html.twig", "", $template);

                // Load defaults if the file exists
                if (file_exists(sprintf("%s/%s.yml", $templatePath, $name))) {
                    $defaults = file_get_contents(sprintf("%s/%s.yml", $templatePath, $name));
                }

                $result[] = [
                    'name'     => $name,
                    'defaults' => $defaults,
                ];
            }
        }

        return $result;
    }

    /**
     * Generate a Unique Slug.
     */
    public function generateSlug(string $text): string
    {
        $i            = 1;
        $originalSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
        $slug         = $originalSlug;

        while (!empty($this->newsletterRepository->findOneBySlug($slug))) {
            $slug = sprintf("%s-%s", $originalSlug, $i);
            $i++;
        }

        return $slug;
    }

    /**
     * Cleanup Cached Data.
     */
    public function cleanupCachedData()
    {
        $configs = $this->configRepository->findManyLike('newsletter_cached_data');

        foreach ($configs as $config) {
            $data = json_decode($config->getValue());

            // Delete data older that two days ago
            if ((new \DateTime($data->date))->format('Y-m-d')
             < (new \DateTime())->modify("-2 days")->format('Y-m-d')) {
                $this->configRepository->remove($config, true);
            }
        }
    }

    /**
     * Count Newsletter By Status.
     */
    public function countAll(): ?int
    {
        return $this->newsletterRepository->countAll();
    }

    /**
     * List Newsletter.
     */
    public function list(int $limit = 20, int $offset = 0): array
    {
        return $this->newsletterRepository->findMany(
            ['createdAt' => 'DESC'],
            $limit,
            $offset
        );
    }
}
