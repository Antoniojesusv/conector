<?php

namespace App\Components;

use App\Connection\Application\List\ListConnectionQuery;
use App\Model\Database\DatabaseService;
use App\Shared\Domain\Bus\Query\Contract\QueryBus;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('table')]
class TableComponent
{
    public function __construct(
        private DatabaseService $databaseService,
        private QueryBus $queryBus
    ) {
        $this->databaseService = $databaseService;
        $this->querybus = $queryBus;
    }

    public function getConnections(): array
    {
        $request = new ListConnectionQuery();
        $result = $this->queryBus->dispatch($request);
        return $result;
        // $result2 = $this->databaseService->listConnections();
    }
}
