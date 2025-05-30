<?php

namespace App\Exceptions;

use Exception;

class VaultException extends Exception
{
    protected int $statusCode = 500;

    public function __construct(string $message, int $statusCode = 500)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
