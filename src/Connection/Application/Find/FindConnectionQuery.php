<?php

declare(strict_types=1);
namespace App\Connection\Application\Find;

use App\Shared\Domain\Bus\Query\Contract\Query;

final class FindConnectionQuery extends Query
{
    public function __construct(
        private readonly string $type
    ) {
        parent::__construct();
    }

    public function getMessageType(): string
    {
        return self::MESSAGE_TYPE;
    }

    public function Type(): string
    {
        return $this->type;
    }

    public function id(): string
    {
        return $this->uuid;
    }
}