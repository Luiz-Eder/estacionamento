<?php

namespace App\Application\UseCase;

use App\Domain\Entity\Car;
use App\Domain\Entity\Motorcycle;
use App\Domain\Entity\Ticket;
use App\Domain\Entity\Truck;
use App\Domain\Repository\TicketRepositoryInterface;
use DateTime;
use Exception;

class RegisterEntry
{
    public function __construct(
        private TicketRepositoryInterface $repository
    ) {}

    public function execute(string $plate, string $type): void
    {
        $vehicle = match (strtolower($type)) {
            'carro' => new Car($plate),
            'moto' => new Motorcycle($plate),
            'caminhao' => new Truck($plate),
            default => throw new Exception("Tipo inválido.")
        };

        $ticket = new Ticket(null, $vehicle, new DateTime());
        $this->repository->save($ticket);
    }
}