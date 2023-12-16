<?php

namespace App\Shared\Domain\Bus\Middleware\Exception;

use Exception;

class ObjectIsNotMiddlewareInstance extends Exception
{
    const MIDDLEWARE_INTERFACE_NAME = "App\Shared\Domain\Bus\Middleware\Contract\Middleware";
    public function __construct(mixed $object)
    {
        $objectClassName = $object::class;
        $middlewareInterfaceName = self::MIDDLEWARE_INTERFACE_NAME;
        parent::__construct("The object: {$objectClassName} is not instance of {$middlewareInterfaceName}");
    }
}