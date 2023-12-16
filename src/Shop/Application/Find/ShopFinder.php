<?php

declare(strict_types=1);
namespace App\Shop\Application\Find;

use App\Article\Application\Synchronization\ArticleSynchronization;
use App\Synchronization\Application\Update\UpdateArticleCommand;

final class ShopFinder
{
    public function __construct(
        private ArticleSynchronization $articleService
    ) {
    }

    public function __invoke(UpdateArticleCommand $updateArticleCommand): void
    {
        $rate = $updateArticleCommand->rate;
        $store = $updateArticleCommand->store;
        $company = $updateArticleCommand->company;

        $this->articleService->synchronise($rate, $store, $company);
    }
}