<?php

namespace App\Model\Database\Entities;

use Exception;

class ConnectionEntity
{
    private string $user = '';
    private ?string $password = '';
    private string $address = '';
    private string $database = '';
    private int $exposedPort = 0;
    private string $type = '';
    private bool $status = false;
    private string $message = '';

    public function __construct(
        string $user,
        ?string $password,
        string $address,
        string $database,
        int $exposedPort,
        string $type,
        bool $status,
        string $message
    ) {
        $this->setUser($user);
        $this->setPassword($password);
        $this->setAddress($address);
        $this->setDatabase($database);
        $this->setExposedPort($exposedPort);
        $this->setType($type);
        $this->status = $status;
        $this->message = $message;
    }

    public function setUser(string $user = ''): void
    {
        if (empty($user)) {
            throw new Exception('The user cannot be empty');
        }

        $this->user = $user;
    }

    public function setPassword(?string $password = ''): void
    {
        if (!is_null($password) && empty($password)) {
            throw new Exception('The password cannot be empty');
        }

        $this->password = $password;
    }

    public function setAddress(string $address = ''): void
    {
        if (empty($address)) {
            throw new Exception('The address cannot be empty');
        }

        $this->address = $address;
    }

    public function setDatabase(string $database = ''): void
    {
        if (empty($database)) {
            throw new Exception('The database cannot be empty');
        }

        $this->database = $database;
    }

    public function setExposedPort(int $exposedPort = 0): void
    {
        if ($exposedPort === 0) {
            throw new Exception('The exposed por cannot be 0');
        }

        $this->exposedPort = $exposedPort;
    }

    public function setType(string $type = ''): void
    {
        if (empty($type)) {
            throw new Exception('The type cannot be empty');
        }

        $this->type = $type;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getExposedPort(): string
    {
        return $this->exposedPort;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
