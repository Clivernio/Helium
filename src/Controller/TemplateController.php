<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Newsletter as NewsletterModule;
use App\Repository\ConfigRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Template Controller.
 */
class TemplateController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var NewsletterModule */
    private $newsletterModule;

    /** KernelInterface $appKernel */
    private $appKernel;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        NewsletterModule $newsletterModule,
        KernelInterface $appKernel
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->newsletterModule = $newsletterModule;
        $this->appKernel        = $appKernel;
    }

    /**
     * Template Navigate Page.
     */
    public function newsletterPreview(string $name): Response
    {
        $this->logger->info("Render newsletter preview page");

        $config = $this->configRepository->findOne($name);

        $defaults = null;

        if (!empty($config)) {
            $data     = json_decode($config->getValue());
            $name     = $data->templateName;
            $defaults = Yaml::parse(trim($data->templateInputs));
        }

        $basePath = rtrim($this->appKernel->getProjectDir(), "/");
        $name     = str_replace("/", "", $name);
        $name     = str_replace("\\", "", $name);

        if (!file_exists(sprintf("%s/templates/default/newsletter/%s.html.twig", $basePath, $name))) {
            throw new NotFoundHttpException(sprintf(
                "Template with name %s not found",
                $name
            ));
        }

        if ((null === $defaults) && file_exists(sprintf("%s/templates/default/newsletter/%s.yml", $basePath, $name))) {
            $defaults = Yaml::parseFile(sprintf(
                "%s/templates/default/newsletter/%s.yml",
                $basePath,
                $name
            ));
        }

        $defaults['unsubscribe_url'] = "#";
        $defaults['subject']         = $this->configRepository->findValueByName("he_app_name", "");

        return $this->render(sprintf('newsletter/%s.html.twig', $name), [
            'app_name'       => $this->configRepository->findValueByName("he_app_name", ""),
            'app_url'        => $this->configRepository->findValueByName("he_app_url", ""),
            'app_email'      => $this->configRepository->findValueByName("he_app_email", ""),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'subject'        => $this->configRepository->findValueByName("he_app_name", ""),
            'data'           => $defaults,
        ]);
    }
}
