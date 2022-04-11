<?php

namespace App\Model\Database\Entities;

use Exception;

class UwConnectionEntity extends ConnectionBaseEntity
{
    public function __construct(
        string $database,
        string $server,
        bool $status,
        string $message
    ) {
        parent::__construct(
            'Usuario de windows',
            'Contraseña de windows',
            $database,
            'sqlServer',
            $status,
            $message
        );
        $this->setServer($server);
    }

    public function getServer(): string
    {
        return $this->server;
    }

    public function setServer(string $server): void
    {
        if (empty($server)) {
            throw new Exception('The server cannot be empty');
        }

        $this->server = $server;
    }
}
