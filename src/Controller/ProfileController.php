<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Controller;

use App\Module\Profile as ProfileModule;
use App\Repository\ConfigRepository;
use App\Service\Validator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Profile Controller.
 */
class ProfileController extends AbstractController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ConfigRepository */
    private $configRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Validator */
    private $validator;

    /** @var ProfileModule */
    private $profileModule;

    /**
     * Class Constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        ConfigRepository $configRepository,
        TranslatorInterface $translator,
        Validator $validator,
        ProfileModule $profileModule
    ) {
        $this->logger           = $logger;
        $this->translator       = $translator;
        $this->configRepository = $configRepository;
        $this->validator        = $validator;
        $this->profileModule    = $profileModule;
    }

    /**
     * Profile Web Page.
     */
    public function profile(): Response
    {
        $this->logger->info("Render profile page");

        return $this->render('page/profile.html.twig', [
            'title'          => $this->configRepository->findValueByName("he_app_name", "Helium"),
            'analytics_code' => $this->configRepository->findValueByName("he_google_analytics_code", ""),
            'user'           => [
                'first_name' => $this->getUser()->getFirstName(),
                'last_name'  => $this->getUser()->getLastName(),
                'job'        => $this->getUser()->getJob(),
                'email'      => $this->getUser()->getEmail(),
            ],
        ]);
    }

    /**
     * Update Profile API Endpoint.
     */
    public function profileEndpoint(Request $request): JsonResponse
    {
        $content = $request->getContent();

        $this->validator->validate($content, "v1/profileAction.schema.json");

        $this->logger->info("Trigger profile v1 endpoint");

        $data = json_decode($content);

        if (empty($data->csrf_token) || !$this->isCsrfTokenValid('profile-action', $data->csrf_token)) {
            throw new InvalidRequest('Invalid request');
        }

        $this->profileModule->updateProfile(
            $this->getUser()->getId(),
            [
                "email"     => $data->email,
                "firstName" => $data->firstName,
                "lastName"  => $data->lastName,
                "jobTitle"  => $data->jobTitle,
            ]
        );

        return $this->json([
            'successMessage' => $this->translator->trans(
                'Profile updated successfully.'
            ),
        ]);
    }
}
