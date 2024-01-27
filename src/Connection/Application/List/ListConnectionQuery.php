<?php

declare(strict_types=1);

namespace App\Connection\Application\List;

use App\Shared\Domain\Bus\Query\Contract\Query;

final class ListConnectionQuery extends Query
{
    public function __construct(
    ) {
        parent::__construct();
    }
}