<?php

namespace App\Domain\Entity;

use DateTime;
use Exception;

class Ticket
{
    private ?DateTime $exitTime = null;

    public function __construct(
        private ?int $id,
        private Vehicle $vehicle,
        private DateTime $entryTime
    ) {}

    public function close(): void
    {
        $this->exitTime = new DateTime();
    }

    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function getEntryTime(): DateTime
    {
        return $this->entryTime;
    }

    public function getExitTime(): ?DateTime
    {
        return $this->exitTime;
    }

    public function calculateTotal(): float
    {
        if ($this->exitTime === null) {
            throw new Exception("O ticket ainda está aberto.");
        }

        $interval = $this->entryTime->diff($this->exitTime);
        $hours = ($interval->d * 24) + $interval->h;
        
        if ($interval->i > 0 || $interval->s > 0) {
            $hours++;
        }

        if ($hours === 0) {
            $hours = 1;
        }

        return $hours * $this->vehicle->getHourlyRate();
    }
}