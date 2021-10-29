<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Weekly project.
 * (c) Clivern <hello@clivern.com>
 */

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
