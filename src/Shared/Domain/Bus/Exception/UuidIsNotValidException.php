<?php

namespace App\Shared\Domain\Bus\Exception;

use Exception;

class UuidIsNotValidException extends Exception
{
    public function __construct(string $uuid)
    {
        parent::__construct("The {$uuid} is not a valid uuid");
    }
}