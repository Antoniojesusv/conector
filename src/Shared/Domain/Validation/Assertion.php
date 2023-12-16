<?php

declare(strict_types=1);

namespace App\Shared\Domain\Validation;

use App\Shared\Domain\Bus\Exception\UuidIsNotValidException;
use Ramsey\Uuid\Uuid;

final class Assertion
{
    public static function isValidUuid(string $uuid)
    {

        if (!Uuid::isValid($uuid)) {
            throw new UuidIsNotValidException($uuid);
        }
    }
}