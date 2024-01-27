<?php

declare(strict_types=1);
namespace App\Shared\Domain\Bus\Query\Contract;

use App\Shared\Domain\Bus\Common\AbstractMessage;


abstract class Query extends AbstractMessage
{
    const MESSAGE_TYPE = 'Query';
}
