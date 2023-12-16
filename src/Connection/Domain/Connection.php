<?php
namespace App\Connection\Domain;

final class Connection
{
    public function __construct(
        private ConnectionId $connectionId,
        private User $user,
        private Password $password,
        private Address $address,
        private Port $port,
        private DatabaseName $databaseName,
        private Type $type,
    ) {
        $this->connectionId = $connectionId;
        $this->user = $user;
        $this->password = $password;
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

    public function password(): string
    {
        return $this->password->value();
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

    public function isEquals(Connection $other): bool
    {
        return $this->id() === $other->id();
    }
}