<?php

declare(strict_types=1);
namespace App\Shop\Domain;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class StoreId
{
    private UuidInterface $uuid;

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function value(): string
    {
        return $this->uuid;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $uuid): self
    {
        return new self(Uuid::fromString($uuid));
    }

    public function isEquals(ShopId $other): bool
    {
        return $this->uuid === $other->value();
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }
}
