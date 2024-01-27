<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Contract;

use App\Shared\Domain\Bus\Contract\BusHandler;
use App\Shared\Domain\Bus\Contract\Message;

interface Resolver
{
    public function getHandlerFor(Message $message): BusHandler;
}