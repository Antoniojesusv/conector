<?php

namespace App\Model\Database\Entities;

use Exception;

class NormalConnectionEntity extends ConnectionEntity
{
    private string $database;
    private int $exposedPort;

    public function __construct(
        string $user,
        string $password,
        string $address,
        string $database,
        int $exposedPort,
        string $type,
        ?bool $status,
        ?string $message
    ) {
        parent::__construct(
            $user,
            $password,
            $address,
            $database,
            $type,
            $status,
            $message
        );
        $this->setDatabase($database);
        $this->setExposedPort($exposedPort);
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
