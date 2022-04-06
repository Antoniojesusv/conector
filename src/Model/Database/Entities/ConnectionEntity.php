<?php

namespace App\Model\Database\Entities;

use Exception;

abstract class ConnectionEntity
{
    const TYPES = ['sqlServer', 'mysqlServer'];

    private string $user;
    private string $password;
    private string $address;
    private string $type;
    private bool $status = false;
    private string $message = '';

    public function __construct(
        string $user,
        string $password,
        string $address,
        string $type,
        ?bool $status,
        ?string $message
    ) {
        $this->setUser($user);
        $this->setPassword($password);
        $this->setAddress($address);
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        if (empty($address)) {
            throw new Exception('The address cannot be empty');
        }

        $this->address = $address;
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

    public function getMessage(): string
    {
        return $this->message;
    }
}
