<?php

declare(strict_types=1);
namespace App\Connection\Application\Edit;

use App\Shared\Domain\Bus\Command\Contract\Command;

final class EditConnectionCommand extends Command
{
    public static function createFromData(
        string $user,
        string $password,
        string $address,
        int $port,
        string $databaseName,
        string $type
    ): self {
        return new self(
            $user,
            $password,
            $address,
            $port,
            $databaseName,
            $type
        );
    }

    private function __construct(
        private readonly string $user,
        private readonly string $password,
        private readonly string $address,
        private readonly int $port,
        private readonly string $databaseName,
        private readonly string $type
    ) {
        parent::__construct();
    }

    public function getMessageType(): string
    {
        return self::MESSAGE_TYPE;
    }

    public function user(): string
    {
        return $this->user;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function address(): string
    {
        return $this->address;
    }

    public function port(): int
    {
        return $this->port;
    }

    public function databaseName(): string
    {
        return $this->databaseName;
    }

    public function Type(): string
    {
        return $this->type;
    }

    public function id(): string
    {
        return $this->uuid;
    }
}