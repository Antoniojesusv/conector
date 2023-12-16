<?php

namespace App\Form;

use App\Connection\Domain\Connection;
use Symfony\Component\Validator\Constraints as Assert;

class NmConnectionModel
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $user;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $password;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $address;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $database;

    /**
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private int $exposedPort;

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user = ''): void
    {
        $this->user = $user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address = ''): void
    {
        $this->address = $address;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database = ''): void
    {
        $this->database = $database;
    }

    public function getExposedPort(): string
    {
        return $this->exposedPort;
    }

    public function setExposedPort(int $exposedPort = 0): void
    {
        $this->exposedPort = $exposedPort;
    }

    public function setData(Connection $connection): void
    {
        $this->user = $connection->user();
        $this->password = $connection->password();
        $this->address = $connection->address();
        $this->database = $connection->databaseName();
        $this->exposedPort = $connection->port();
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'password' => $this->password,
            'address' => $this->address,
            'database' => $this->database,
            'exposedPort' => $this->exposedPort
        ];
    }
}
