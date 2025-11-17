<?php

namespace App\Domain\Entity;

class Car extends Vehicle
{
    public function getHourlyRate(): float
    {
        return 5.00;
    }
}