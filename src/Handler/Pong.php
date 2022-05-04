<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Handler;

use App\Message\Ping as PingMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Ping Message Handler.
 */
#[AsMessageHandler]
class Pong
{
    /**
     * Invoke handler.
     */
    public function __invoke(PingMessage $message)
    {
        // ...
    }
}
