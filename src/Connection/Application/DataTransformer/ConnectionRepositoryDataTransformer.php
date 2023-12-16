<?php

declare(strict_types=1);
namespace App\Connection\Application\DataTransformer;

use App\Connection\Application\ConnectionFactory;
use App\Connection\Domain\Connection;
use Ramsey\Uuid\Uuid;

class ConnectionRepositoryDataTransformer
{
    public function __construct(private ConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }
    public function transform(array $connectionParams): Connection
    {
        $connectionParams['connectionId'] = Uuid::uuid4()->toString();
        $connectionParams['port'] = $connectionParams['exposedPort'];
        $connectionParams['databaseName'] = $connectionParams['database'];

        unset($connectionParams['exposedPort']);
        unset($connectionParams['database']);
        unset($connectionParams['status']);

        $connectionParams['port'] = (int) $connectionParams['port'];

        return $this->connectionFactory->create(...$connectionParams);
    }
}