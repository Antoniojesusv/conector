<?php

declare(strict_types=1);
namespace App\Synchronisation\Domain;

interface SourceArticleRepository
{
    public function getAllByRateStockStoreAndCompany(
        string $rate,
        string $store,
        string $company
    ): array;
}
