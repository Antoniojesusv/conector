<?php

declare(strict_types=1);
namespace App\Connection\Application\DataTransformer;

use App\Connection\Application\ConnectionSecurityFactory;
use App\Connection\Domain\ConnectionSecurity;
use Ramsey\Uuid\Uuid;

class ConnectionSecurityRepositoryDataTransformer
{
    public function __construct(private ConnectionSecurityFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }
    public function transform(array $connectionParams): ConnectionSecurity
    {
        $connectionParams['connectionId'] = Uuid::uuid4()->toString();
        $connectionParams['port'] = $connectionParams['exposedPort'];
        $connectionParams['databaseName'] = $connectionParams['database'];

        unset($connectionParams['exposedPort']);
        unset($connectionParams['database']);
        unset($connectionParams['password']);

        $connectionParams['port'] = (int) $connectionParams['port'];

        return $this->connectionFactory->create(...$connectionParams);
    }
}