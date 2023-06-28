<?php

declare(strict_types=1);
namespace App\Synchronisation\Application\Update;

use App\Shared\Domain\Bus\Command\Command;

final class UpdateArticleCommand implements Command
{
    public function __construct(
        public readonly string $rate,
        public readonly string $store,
        public readonly string $company
    ) {
        $this->rate = $rate;
        $this->store = $store;
        $this->company = $company;
    }
}
