<?php

namespace App\Model\Synchronisation;

interface ArticleRepositoryI
{
    public function save(iterable $entityList): void;
}
