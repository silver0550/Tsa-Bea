<?php

namespace App\Models\Dfa;

use App\Contracts\StateInterface;

class NumberStateMachine
{
    private StateInterface $currentState;

    public function __construct()
    {
        $this->currentState = new StateA();
    }

    public function validate(string $input): bool
    {
        $this->currentState = new StateA();

        $length = strlen($input);

        for ($i = 0; $i < $length; $i++) {
            $char = $input[$i];
            $nextState = $this->currentState->transition($char);

            if ($nextState === null) {
                return false;
            }

            $this->currentState = $nextState;
        }

        return $this->currentState->isAccepting();
    }

    public function getCurrentStateName(): string
    {
        return $this->currentState->getName();
    }
}
