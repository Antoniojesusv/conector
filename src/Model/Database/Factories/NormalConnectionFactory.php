<?php

namespace App\Model\Database\Factories;

use App\Model\Database\Entities\ConnectionEntity;
use App\Model\Database\Entities\NormalConnectionEntity;

class NormalConnectionFactory extends ConnectionFactory
{
    public function create($params): ConnectionEntity
    {
        [
            'user' => $user,
            'password' => $password,
            'address' => $address,
            'database' => $database,
            'exposedPort' => $exposedPort,
            'status' => $status,
            'message' => $message
        ] = $params;

        $connectionEntity = new NormalConnectionEntity(
            $user,
            $password,
            $address,
            $database,
            $exposedPort,
            'mysqlServer',
            $status,
            $message
        );

        return $connectionEntity;
    }
}
