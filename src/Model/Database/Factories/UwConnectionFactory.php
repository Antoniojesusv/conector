<?php

namespace App\Model\Database\Factories;

use App\Model\Database\Entities\ConnectionBaseEntity;
use App\Model\Database\Entities\UwConnectionEntity;

class UwConnectionFactory implements ConnectionFactoryI
{
    public function create(array $params): ConnectionBaseEntity
    {
        [
            'database' => $database,
            'server' => $server,
            'status' => $status,
            'message' => $message
        ] = $params;

        $connectionEntity = new UwConnectionEntity($database, $server, $status, $message);

        return $connectionEntity;
    }
}
