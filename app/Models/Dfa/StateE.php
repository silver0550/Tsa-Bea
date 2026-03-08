<?php

namespace App\Models\Dfa;

class StateE extends BaseState
{
    public function transition(string $char): ?BaseState
    {
        return match (true){
            $this->isDigit($char) => new StateF(),
            default => null
        };
    }

    public function isAccepting(): bool
    {
        return false;
    }

    public function getName(): string
    {
        return 'E';
    }
}
