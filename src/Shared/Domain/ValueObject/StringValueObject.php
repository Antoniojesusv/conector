<?php

declare(strict_types=1);
namespace App\Shared\Domain\ValueObject;

abstract class StringValueObject
{
    public function __construct(protected readonly string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEquals(StringValueObject $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
