<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Weekly project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
