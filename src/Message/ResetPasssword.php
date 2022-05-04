<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Message;

/**
 * Reset Passsword Message.
 */
class ResetPasssword
{
    /**
     * @var string
     */
    private $content;

    /**
     * Class Constructor.
     */
    public function __construct(array $content)
    {
        $this->content = json_encode($content);
    }

    /**
     * Get Content.
     */
    public function getContent(): array
    {
        return json_decode($this->content, true);
    }
}
