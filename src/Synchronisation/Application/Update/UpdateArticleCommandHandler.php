<?php

declare(strict_types=1);
namespace App\Synchronisation\Application\Update;

use App\Shared\Domain\Bus\Command\CommandHandler;

final class UpdateArticleCommandHandler implements CommandHandler
{
    public function __construct(
        private ArticleSynchronisation $articleService
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
