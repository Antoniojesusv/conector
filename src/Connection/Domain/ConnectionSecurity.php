<?php
namespace App\Connection\Domain;

final class ConnectionSecurity
{
    public function __construct(
        private ConnectionId $connectionId,
        private User $user,
        private Address $address,
        private Port $port,
        private DatabaseName $databaseName,
        private Type $type,
        private Status $status
    ) {
        $this->connectionId = $connectionId;
        $this->user = $user;
        $this->address = $address;
        $this->port = $port;
        $this->databaseName = $databaseName;
        $this->type = $type;
    }

    public function id(): string
    {
        return $this->connectionId->value();
    }

    public function user(): string
    {
        return $this->user->value();
    }

    public function address(): string
    {
        return $this->address->value();
    }

    public function port(): int
    {
        return $this->port->value();
    }

    public function databaseName(): string
    {
        return $this->databaseName->value();
    }

    public function type(): string
    {
        return $this->type->value();
    }

    public function status(): bool
    {
        return $this->status->value();
    }

    public function isEquals(Connection $other): bool
    {
        return $this->id() === $other->id();
    }
}