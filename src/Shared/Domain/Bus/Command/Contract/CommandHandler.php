<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Command\Contract;

interface CommandHandler
{
    public function __invoke(Command $message): mixed;
}