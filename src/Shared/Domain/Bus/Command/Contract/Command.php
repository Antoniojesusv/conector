<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Command\Contract;

use App\Shared\Domain\Bus\Common\AbstractMessage;


abstract class Command extends AbstractMessage
{
    const MESSAGE_TYPE = 'Command';
}