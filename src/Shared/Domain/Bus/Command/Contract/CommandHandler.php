<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Command\Contract;

use App\Shared\Domain\Bus\Contract\BusHandler;

interface CommandHandler extends BusHandler
{
    public function __invoke(Command $command): void;
}