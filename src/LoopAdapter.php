<?php

namespace App;

use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;

class LoopAdapter
{
    public function get(): LoopInterface
    {
        return Loop::get();
    }
}
