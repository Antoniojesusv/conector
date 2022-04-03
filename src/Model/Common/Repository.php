<?php

namespace App\Common;

abstract class Repository
{
    abstract public function save($entity): void;
}
