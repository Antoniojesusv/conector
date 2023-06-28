<?php
namespace App\Synchronisation\Domain;

class Deregister
{
    public function __construct(
        private readonly int $value
    ) {
        $this->setValue($value);
    }

    public function value(): bool
    {
        return $this->value;
    }

    public function isEquals(s01 $other): bool
    {
        return $this->value === $other->value();
    }

    private function setValue(int $value): void
    {
        $this->value = !($value === 0);
    }
}
