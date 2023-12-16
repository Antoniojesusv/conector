<?php

declare(strict_types=1);
namespace App\Connection\Application\Find;

use App\Connection\Infrastructure\Persistance\Pdo\ConnectionRepository;
use App\Shared\Domain\Bus\Query\Contract\Query;
use App\Shared\Domain\Bus\Query\Contract\QueryHandler;

final class FindConnectionQueryHandler implements QueryHandler
{
    public function __construct(
        private ConnectionRepository $connectionRepository
    ) {
        $this->connectionRepository = $connectionRepository;
    }

    /**
     * Summary of __invoke
     * @param \App\Shared\Domain\Bus\Query\Contract\Query $query
     * @return mixed
     */
    public function __invoke(Query $query): mixed
    {
        return $this->connectionRepository->getByType($query->type());
    }
}