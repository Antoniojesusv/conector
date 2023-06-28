<?php
namespace App\Synchronisation\Domain;

use App\Synchronisation\Domain\Exception\S01IsNull;

class S01
{
    public function __construct(
        private readonly ?string $value
    ) {
        $this->setValue($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEquals(s01 $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    private function setValue(?string $value): void
    {
        if (is_null($value)) {
            throw new S01IsNull('El valor s01 no puede ser NULO');
        }

        $this->value = $value;
    }
}
