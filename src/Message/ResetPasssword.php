<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Helium project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Message;

/**
 * Reset Passsword Message.
 */
class ResetPasssword implements MessageInterface
{
    /**
     * @var string
     */
    private $content;

    /**
     * Class Constructor.
     */
    public function __construct(array $content = [])
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

    /**
     * Set Content.
     */
    public function setContent(array $content = [])
    {
        $this->content = json_encode($content);
    }
}
