<?php

namespace App\Models\Dfa;

class StateA extends BaseState
{
    public function transition(string $char): ?BaseState
    {
        return match (true){
            $char === '-' => new StateB(),
            $char === '0' => new StateC(),
            $this->isNonZeroDigit($char) => new StateD(),
            default => null
        };
    }

    public function isAccepting(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return 'A';
    }
}
