<?php

namespace App\Contracts;

interface StateInterface
{
    public function transition(string $char): ?StateInterface;

    public function isAccepting(): bool;

    public function getName(): string;
}
