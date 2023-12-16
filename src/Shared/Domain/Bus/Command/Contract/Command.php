<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Command\Contract;

use App\Shared\Domain\Bus\Contract\BaseMessage;

abstract class Command extends BaseMessage
{
    const MESSAGE_TYPE = 'command';
}