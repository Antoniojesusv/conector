<?php

namespace App\Model\Synchronisation;

use App\Repository\ArticleProductRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ArticlesSynchronisationService
{
    private ContainerBagInterface $params;
    private ArticleProductRepository $articleRepository;
    
    public function __construct(
        ContainerBagInterface $params,
        ArticleProductRepository $articleRepository
    ) {
        $this->params = $params;
        $this->articleRepository = $articleRepository;
    }

    public function synchronise(): void
    {
        $rate = $this->params->get('shop.rate');
        $store = '00';
        $company = '01';

        $entityList = $this->articleRepository->getAllByRateStockStoreAndCompany($rate, $store, $company);
        $this->articleRepository->save($entityList);
    }
}
