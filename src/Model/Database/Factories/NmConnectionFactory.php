<?php

namespace App\Model\Database\Factories;

use App\Model\Database\Entities\ConnectionBaseEntity;
use App\Model\Database\Entities\NmConnectionEntity;

class NmConnectionFactory implements ConnectionFactoryI
{
    public function create($params): ConnectionBaseEntity
    {
        [
            'user' => $user,
            'password' => $password,
            'address' => $address,
            'database' => $database,
            'exposedPort' => $exposedPort,
            'type' => $type,
            'status' => $status,
            'message' => $message
        ] = $params;

        $connectionEntity = new NmConnectionEntity(
            $user,
            $password,
            $address,
            $database,
            $exposedPort,
            $type,
            $status,
            $message
        );

        return $connectionEntity;
    }
}
