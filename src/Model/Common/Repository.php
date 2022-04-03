<?php

namespace App\Model\Common;

abstract class Repository
{
    abstract public function save($entity): void;
}
