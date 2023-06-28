<?php
namespace App\Synchronisation\Domain;

class CodeId
{
    public function __construct(
        public readonly string $id
    ) {
        $this->id;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function isEquals(CodeId $other): bool
    {
        return $this->id === $other->id();
    }

    public function __toString(): string
    {
        return $this->id();
    }
}
