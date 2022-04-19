<?php

namespace App\Model\Synchronisation;

interface ArticleRepositoryI
{
    public function save(array $entityList): void;
}
