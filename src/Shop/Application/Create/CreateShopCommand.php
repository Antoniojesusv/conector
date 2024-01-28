<?php

declare(strict_types=1);

namespace App\Shop\Application\Create;

use App\Shared\Domain\Bus\Command\Contract\Command;

final class CreateShopCommand extends Command
{
    public function __construct(
        private readonly string $name,
        private readonly string $rate,
        private readonly string $store,
    ) {
        parent::__construct();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function rate(): string
    {
        return $this->rate;
    }

    public function store(): string
    {
        return $this->store;
    }
}