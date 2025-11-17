<?php

namespace App\Domain\Entity;

class Truck extends Vehicle
{
    public function getHourlyRate(): float
    {
        return 10.00;
    }
}