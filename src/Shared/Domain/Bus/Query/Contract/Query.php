<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Query\Contract;

use App\Shared\Domain\Bus\Contract\BaseMessage;

abstract class Query extends BaseMessage
{
    const MESSAGE_TYPE = 'query';
}
