<?php

declare(strict_types=1);
namespace App\Connection\Application;

use App\Connection\Domain\ConnectionId;
use App\Connection\Domain\ConnectionSecurity;
use App\Connection\Domain\DatabaseName;
use App\Connection\Domain\Address;
use App\Connection\Domain\Port;
use App\Connection\Domain\Status;
use App\Connection\Domain\Type;
use App\Connection\Domain\User;

class ConnectionSecurityFactory
{
    public function create(
        string $connectionId,
        string $user,
        string $address,
        int $port,
        string $databaseName,
        string $type,
        bool $status
    ): ConnectionSecurity {
        $connectionIdValueObject = new ConnectionId($connectionId);
        $userValueObject = new User($user);
        $addressValueObject = new Address($address);
        $portValueObject = new Port($port);
        $databaseNameValueObject = new DatabaseName($databaseName);
        $typeValueObject = new Type($type);
        $statusValueObject = new Status($status);

        return new ConnectionSecurity(
            $connectionIdValueObject,
            $userValueObject,
            $addressValueObject,
            $portValueObject,
            $databaseNameValueObject,
            $typeValueObject,
            $statusValueObject
        );
    }
}