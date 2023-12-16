<?php

declare(strict_types=1);
namespace App\Connection\Application;

use App\Connection\Domain\Address;
use App\Connection\Domain\Connection;
use App\Connection\Domain\ConnectionId;
use App\Connection\Domain\DatabaseName;
use App\Connection\Domain\Password;
use App\Connection\Domain\Port;
use App\Connection\Domain\Type;
use App\Connection\Domain\User;

class ConnectionFactory
{
    public function create(
        string $connectionId,
        string $user,
        string $password,
        string $address,
        int $port,
        string $databaseName,
        string $type,
    ): Connection {
        $connectionIdValueObject = new ConnectionId($connectionId);
        $userValueObject = new User($user);
        $passwordValueObject = new Password($password);
        $addressValueObject = new Address($address);
        $portValueObject = new Port($port);
        $databaseNameValueObject = new DatabaseName($databaseName);
        $typeValueObject = new Type($type);

        return new Connection(
            $connectionIdValueObject,
            $userValueObject,
            $passwordValueObject,
            $addressValueObject,
            $portValueObject,
            $databaseNameValueObject,
            $typeValueObject
        );
    }
}