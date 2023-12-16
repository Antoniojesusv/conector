<?php

declare(strict_types=1);
namespace App\Shared\Domain\ValueObject;

;
use App\Shared\Domain\Validation\Assertion;
use Ramsey\Uuid\Uuid as RamseyUuid;

abstract class UuidValueObject
{
    public function __construct(protected readonly string $uuid)
    {
        Assertion::isValidUuid($uuid);
    }

    public static function random(): self
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public function value(): string
    {
        return $this->uuid;
    }

    public function isEquals(UuidValueObject $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
