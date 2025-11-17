<?php

namespace App\Domain\Entity;

class Motorcycle extends Vehicle
{
    public function getHourlyRate(): float
    {
        return 3.00;
    }
}