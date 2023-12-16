<?php

declare(strict_types=1);
namespace App\Shared\Infrastructure\Pdo\Dbal\Contract;

interface PdoManager
{
    public function connect(): void;
    public function getConnection(): \PDO;
    public function reconnect(): void;
    public function hasConnection(): bool;
}