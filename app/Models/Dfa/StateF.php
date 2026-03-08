<?php

namespace App\Models\Dfa;

class StateF extends BaseState
{
    public function transition(string $char): ?BaseState
    {
        return match (true){
            $this->isDigit($char) => new self(),
            default => null
        };
    }

    public function isAccepting(): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'F';
    }
}
