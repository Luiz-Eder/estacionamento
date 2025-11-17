<?php

namespace App\Domain\Entity;

abstract class Vehicle
{
    public function __construct(
        protected string $plate
    ) {}

    public function getPlate(): string
    {
        return $this->plate;
    }

    abstract public function getHourlyRate(): float;
}