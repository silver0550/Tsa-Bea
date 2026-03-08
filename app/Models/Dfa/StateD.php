<?php

namespace App\Models\Dfa;

class StateD extends BaseState
{
    public function transition(string $char): ?BaseState
    {
        return match (true){
            $this->isDigit($char) => new StateD(),
            $char === '.' => new StateE(),
            default => null
        };
    }

    public function isAccepting(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'D';
    }
}
