<?php

namespace App\Components;

use App\Model\Database\DatabaseService;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('table')]
class TableComponent
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService= $databaseService;
    }

    public function getConnections(): array
    {
        return $this->databaseService->listConnections();
    }
}
