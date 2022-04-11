<?php

namespace App\Form;

use App\Model\Database\Entities\NmConnectionEntity;
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

    public function setPassword(?string $password = ''): void
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

    public function setData(NmConnectionEntity $connection): void
    {
        $this->user = $connection->getUser();
        $this->password = $connection->getPassword();
        $this->address = $connection->getAddress();
        $this->database = $connection->getDatabase();
        $this->exposedPort = $connection->getExposedPort();
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
