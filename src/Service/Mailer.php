<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Mailer Service.
 */
class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * Class Constructor.
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send a Message.
     */
    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $data = []
    ): void {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to(new Address($to))
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($data);

        $this->mailer->send($email);
    }
}
