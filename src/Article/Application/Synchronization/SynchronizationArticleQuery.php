<?php

declare(strict_types=1);
namespace App\Article\Application\Synchronization;

use App\Shared\Domain\Bus\Query\Contract\Query;

final class SynchronizationArticleQuery extends Query
{
    public function __construct(
        private readonly string $rate,
        private readonly string $store,
        private readonly string $company,
    ) {
        parent::__construct();
    }

    public function rate(): string
    {
        return $this->rate;
    }

    public function store(): string
    {
        return $this->store;
    }

    public function company(): string
    {
        return $this->company;
    }
}