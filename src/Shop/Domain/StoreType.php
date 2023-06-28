<?php

declare(strict_types=1);
namespace App\Shop\Domain;

enum StoreType: string
{
    case all = 'all';
    case zeroCero = '00';
    case zeroOne = '01';
    case fiftyFive = '55';
    case eo = 'eo';
    case il = 'il';
}
