<?php

declare(strict_types=1);
namespace App\Synchronisation\Application\Update;

use App\Synchronisation\Domain\SourceArticleRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ArticleSynchronisation
{
    public function __construct(
        private ContainerBagInterface $params,
        private SourceArticleRepository $eurowinArticleRepository
    ) {
        $this->eurowinArticleRepository = $eurowinArticleRepository;
    }

    public function synchronise(
        string $rate,
        string $store,
        string $company
    ): void {
        $entityList = $this->eurowinArticleRepository->getAllByRateStockStoreAndCompany($rate, $store, $company);

        // $this->eurowinArticleRepository->save($entityList);
        $hola = '2';
    }
}
