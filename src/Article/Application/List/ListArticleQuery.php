<?php

declare(strict_types=1);
namespace App\Article\Application\List;

use App\Shared\Domain\Bus\Query\Contract\Query;

final class ListArticleQuery extends Query
{
    public function __construct(
    ) {
        parent::__construct();
    }
}