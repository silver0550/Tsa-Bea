<?php

namespace App\Models\Dfa;

class StateC extends BaseState
{
    public function transition(string $char): ?BaseState
    {
        return match (true){
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
        return 'C';
    }
}
