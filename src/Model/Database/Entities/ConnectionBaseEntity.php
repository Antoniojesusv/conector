<?php

namespace App\Model\Database\Entities;

use Exception;

abstract class ConnectionBaseEntity
{
    const TYPES = ['sqlServer', 'mysqlServer'];

    protected string $user;
    protected string $password;
    protected string $database;
    protected string $type;
    protected bool $status = false;
    protected string $message = '';

    public function __construct(
        string $user,
        string $password,
        string $database,
        string $type,
        bool $status,
        string $message
    ) {
        $this->setUser($user);
        $this->setPassword($password);
        $this->setDatabase($database);
        $this->setType($type);
        $this->status = $status;
        $this->message = $message;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): void
    {
        if (empty($user)) {
            throw new Exception('The user cannot be empty');
        }

        $this->user = $user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        if (!is_null($password) && empty($password)) {
            throw new Exception('The password cannot be empty');
        }

        $this->password = $password;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database): void
    {
        if (empty($database)) {
            throw new Exception('The database cannot be empty');
        }

        $this->database = $database;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        if (empty($type)) {
            throw new Exception('The type cannot be empty');
        }

        if (!in_array($type, $this::TYPES)) {
            throw new Exception("the $type is not a vale type");
        }

        $this->type = $type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}
