<?php

namespace App\Model\Database\Entities;

use Exception;

class NmConnectionEntity extends ConnectionBaseEntity
{
    private string $address;
    private int $exposedPort;

    public function __construct(
        string $user,
        string $password,
        string $address,
        string $database,
        int $exposedPort,
        string $type,
        bool $status,
        string $message
    ) {
        parent::__construct(
            $user,
            $password,
            $database,
            $type,
            $status,
            $message
        );
        $this->setAddress($address);
        $this->setExposedPort($exposedPort);
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

    public function getExposedPort(): string
    {
        return $this->exposedPort;
    }

    public function setExposedPort(int $exposedPort): void
    {
        if ($exposedPort === 0) {
            throw new Exception('The exposed por cannot be 0');
        }

        $this->exposedPort = $exposedPort;
    }
}
