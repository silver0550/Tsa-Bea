<?php

namespace App\Models\Dfa;

use App\Contracts\StateInterface;

abstract class BaseState implements StateInterface
{
    public function isDigit(string $char): bool
    {
        return $char >= '0' && $char <= '9';
    }

    public function isNonZeroDigit(string $char): bool
    {
        return $char >= '1' && $char <= '9';
    }
}
