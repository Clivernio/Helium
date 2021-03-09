<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Handler;

use App\Message\Newsletter as NewsletterMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Newsletter Message Handler.
 */
#[AsMessageHandler]
class SendNewsletter
{
    /**
     * Invoke handler.
     */
    public function __invoke(NewsletterMessage $message)
    {
        // ...
    }
}
