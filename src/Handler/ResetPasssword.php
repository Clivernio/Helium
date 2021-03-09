<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Handler;

use App\Message\ResetPasssword as ResetPassswordMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Reset Passsword Message Handler.
 */
#[AsMessageHandler]
class ResetPasssword
{
    /**
     * Invoke handler.
     */
    public function __invoke(ResetPassswordMessage $message)
    {
        // ...
    }
}
