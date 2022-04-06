<?php

namespace App\Model\Database\Factories;

use App\Model\Database\Entities\ConnectionEntity;
use App\Model\Database\Entities\UserWindowsConnectionEntity;

class UserWindowsConnectionFactory extends ConnectionFactory
{
    public function create(array $params): ConnectionEntity
    {
        [
            'address' => $address,
            'status' => $status,
            'message' => $message
        ] = $params;

        $connectionEntity = new UserWindowsConnectionEntity($address, $status, $message);

        return $connectionEntity;
    }
}
