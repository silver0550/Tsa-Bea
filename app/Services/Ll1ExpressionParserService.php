<?php

namespace App\Services;

use InvalidArgumentException;

class Ll1ExpressionParserService
{
    private string $input = '';
    private int $pos = 0;
    private array $productions = [];

    public function parse(string $expression): array
    {
        $this->input = preg_replace('/\s+/', '', $expression) . '$';
        $this->pos = 0;
        $this->productions = [];

        $value = $this->E();
        $this->match('$');

        return [
            'accepted' => true,
            'value' => $value,
            'productions' => $this->productions,
        ];
    }

    private function lookAhead(): string
    {
        return $this->input[$this->pos] ?? '$';
    }

    private function match(string $char): void
    {
        if ($this->lookAhead() === $char) {
            $this->pos++;
            return;
        }

        throw new InvalidArgumentException(
            "Szintaktikai hiba a(z) {$this->pos}. pozíción. Várt: '{$char}', kapott: '{$this->lookAhead()}'."
        );
    }

    private function startsWith(string $text): bool
    {
        return substr($this->input, $this->pos, strlen($text)) === $text;
    }

    private function consumeWord(string $word): void
    {
        if (! $this->startsWith($word)) {
            throw new InvalidArgumentException(
                "Szintaktikai hiba a(z) {$this->pos}. pozíción. Várt token: '{$word}'."
            );
        }

        $this->pos += strlen($word);
    }

    private function isDigit(string $char): bool
    {
        return $char >= '0' && $char <= '9';
    }

    /*
     * E  -> T E'
     * E' -> + T E' | - T E' | e
     * T  -> F T'
     * T' -> * F T' | / F T' | e
     * F  -> sin(F) | cos(F) | (E) | N
     * N  -> D N'
     * N' -> D N' | e
     * D  -> 0 | 1 | ... | 9
     */

    private function E(): float
    {
        if (
            $this->isDigit($this->lookAhead()) ||
            $this->lookAhead() === '(' ||
            $this->startsWith('sin') ||
            $this->startsWith('cos')
        ) {
            $this->productions[] = "E -> T E'";
            $value = $this->T();
            return $value + $this->EPrime();
        }

        throw new InvalidArgumentException("Hibás kifejezés: E nem vezethető le.");
    }

    private function EPrime(): float
    {
        if ($this->lookAhead() === '+' || $this->lookAhead() === '-') {
            $operator = $this->lookAhead();
            $this->productions[] = "E' -> {$operator} T E'";
            $this->match($operator);

            $value = $this->T();
            $rest = $this->EPrime();

            if ($operator === '-') {
                $value *= -1;
            }

            return $value + $rest;
        }

        if ($this->lookAhead() === ')' || $this->lookAhead() === '$') {
            $this->productions[] = "E' -> e";
            return 0.0;
        }

        throw new InvalidArgumentException("Hibás kifejezés: E' nem vezethető le.");
    }

    private function T(): float
    {
        if (
            $this->isDigit($this->lookAhead()) ||
            $this->lookAhead() === '(' ||
            $this->startsWith('sin') ||
            $this->startsWith('cos')
        ) {
            $this->productions[] = "T -> F T'";
            $value = $this->F();
            return $value * $this->TPrime();
        }

        throw new InvalidArgumentException("Hibás kifejezés: T nem vezethető le.");
    }

    private function TPrime(): float
    {
        if ($this->lookAhead() === '*' || $this->lookAhead() === '/') {
            $operator = $this->lookAhead();
            $this->productions[] = "T' -> {$operator} F T'";
            $this->match($operator);

            $value = $this->F();

            if ($operator === '/') {
                if ($value == 0.0) {
                    throw new InvalidArgumentException('Nullával való osztás nem megengedett.');
                }

                $value = 1 / $value;
            }

            return $value * $this->TPrime();
        }

        if (
            $this->lookAhead() === '+' ||
            $this->lookAhead() === '-' ||
            $this->lookAhead() === ')' ||
            $this->lookAhead() === '$'
        ) {
            $this->productions[] = "T' -> e";
            return 1.0;
        }

        throw new InvalidArgumentException("Hibás kifejezés: T' nem vezethető le.");
    }

    private function F(): float
    {
        if ($this->startsWith('sin')) {
            $this->productions[] = "F -> sin(F)";
            $this->consumeWord('sin');
            $this->match('(');
            $value = $this->E();
            $this->match(')');

            return sin($value);
        }

        if ($this->startsWith('cos')) {
            $this->productions[] = "F -> cos(F)";
            $this->consumeWord('cos');
            $this->match('(');
            $value = $this->E();
            $this->match(')');

            return cos($value);
        }

        if ($this->lookAhead() === '(') {
            $this->productions[] = "F -> (E)";
            $this->match('(');
            $value = $this->E();
            $this->match(')');

            return $value;
        }

        if ($this->isDigit($this->lookAhead())) {
            $this->productions[] = "F -> N";
            return $this->N();
        }

        throw new InvalidArgumentException("Hibás kifejezés: F nem vezethető le.");
    }

    private function N(): float
    {
        if ($this->isDigit($this->lookAhead())) {
            $this->productions[] = "N -> D N'";
            $digit = $this->D();
            $rest = $this->NPrime();

            return (float) ($digit . $rest);
        }

        throw new InvalidArgumentException("Hibás kifejezés: N nem vezethető le.");
    }

    private function NPrime(): string
    {
        if ($this->isDigit($this->lookAhead())) {
            $this->productions[] = "N' -> D N'";
            $digit = $this->D();

            return (string) $digit . $this->NPrime();
        }

        if (
            $this->lookAhead() === '+' ||
            $this->lookAhead() === '-' ||
            $this->lookAhead() === '*' ||
            $this->lookAhead() === '/' ||
            $this->lookAhead() === ')' ||
            $this->lookAhead() === '$'
        ) {
            $this->productions[] = "N' -> e";
            return '';
        }

        throw new InvalidArgumentException("Hibás kifejezés: N' nem vezethető le.");
    }

    private function D(): int
    {
        $char = $this->lookAhead();

        if ($this->isDigit($char)) {
            $this->productions[] = "D -> {$char}";
            $this->match($char);

            return (int) $char;
        }

        throw new InvalidArgumentException("Hibás kifejezés: D nem vezethető le.");
    }
}
