<?php

namespace App\Form;

use App\Model\Database\Entities\UwConnectionEntity;
use Symfony\Component\Validator\Constraints as Assert;

class UwConnectionModel
{
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $database;
    
    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private string $server;

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function setDatabase(string $database = ''): void
    {
        $this->database = $database;
    }

    public function getServer(): string
    {
        return $this->server;
    }

    public function setServer(string $server = ''): void
    {
        $this->server = $server;
    }

    public function setData(UwConnectionEntity $connection): void
    {
        $this->database = $connection->getDatabase();
        $this->server = $connection->getServer();
    }

    public function toArray(): array
    {
        return [
            'database' => $this->database,
            'server' => $this->server
        ];
    }
}
