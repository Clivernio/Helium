<?php

declare(strict_types=1);

/*
 * This file is part of the Clivern/Midway project.
 * (c) Clivern <hello@clivern.com>
 */

namespace App\Service;

use App\Exception\InvalidRequest;
use Exception;
use JsonSchema\Validator as JsonValidator;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Validator Class.
 */
class Validator
{
    /** @var KernelInterface */
    private $appKernel;

    /**
     * Class Constructor.
     */
    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    /**
     * Validate JSON against JSON Schema.
     */
    public function validate(string $data, string $schemaName): bool
    {
        $errors = $this->check($data, $schemaName);

        if (!empty($errors)) {
            throw new InvalidRequest($errors[0]);
        }

        return true;
    }

    /**
     * Check JSON against JSON Schema.
     */
    public function check(string $data, string $schemaName): array
    {
        try {
            $data    = empty(trim($data)) ? '{}' : $data;
            $dataObj = json_decode($data);

            $validator = new JsonValidator();

            $validator->validate(
                $dataObj,
                (object) [
                    '$ref' => 'file://' . realpath(sprintf(
                        '%s/schemas/%s',
                        $this->appKernel->getProjectDir(),
                        $schemaName
                    )),
                ]
            );

            $messages = [];

            if ($validator->isValid()) {
                return $messages;
            }

            foreach ($validator->getErrors() as $error) {
                $messages[] = $error['property'] . ': ' . $error['message'];
            }

            return $messages;
        } catch (Exception $e) {
            throw new InvalidRequest('Invalid request');
        }
    }
}
