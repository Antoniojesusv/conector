<?php

namespace App\Model\Database\Entities;

class UserWindowsConnectionEntity extends ConnectionEntity
{
    public function __construct(
        string $address,
        bool $status,
        string $message
    ) {
        parent::__construct(
            'Usuario de windows',
            'Contraseña de windows',
            $address,
            'sqlServer',
            $status,
            $message
        );
    }
}
