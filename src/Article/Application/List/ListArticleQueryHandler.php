<?php

declare(strict_types=1);
namespace App\Article\Application\List;

use App\Article\Infrastructure\Persistance\Pdo\ArticleRepository;
use App\Shared\Domain\Bus\Query\Contract\Query;
use App\Shared\Domain\Bus\Query\Contract\QueryHandler;

final class ListArticleQueryHandler implements QueryHandler
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Summary of __invoke
     * @param \App\Shared\Domain\Bus\Query\Contract\Query $query
     * @return mixed
     */
    public function __invoke(Query $query): mixed
    {
        return $this->articleRepository->getAll();
    }
}