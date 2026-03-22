<?php

namespace App\Models;

class City
{

    public function __construct(private string $name, private float $x, private float $y)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['name'], $data['x'], $data['y']);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'x' => $this->x,
            'y' => $this->y
        ];
    }

    public function distanceTo(City $other): float
    {
        return sqrt(
            ($this->x - $other->x) ** 2
            + ($this->y - $other->y) ** 2
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function setX(float $x): void
    {
        $this->x = $x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setY(float $y): void
    {
        $this->y = $y;
    }
}
