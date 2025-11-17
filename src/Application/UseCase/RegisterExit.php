<?php

namespace App\Application\UseCase;

use App\Domain\Repository\TicketRepositoryInterface;
use Exception;

class RegisterExit
{
    public function __construct(
        private TicketRepositoryInterface $repository
    ) {}

    public function execute(string $plate): array
    {
        $tickets = $this->repository->findAll();
        $ticketToClose = null;

        foreach ($tickets as $ticket) {
            if ($ticket->getVehicle()->getPlate() === $plate && $ticket->getExitTime() === null) {
                $ticketToClose = $ticket;
                break;
            }
        }

        if (!$ticketToClose) {
            throw new Exception("Veículo não encontrado ou já saiu.");
        }

        $ticketToClose->close();
        $this->repository->update($ticketToClose);

        return [
            'plate' => $ticketToClose->getVehicle()->getPlate(),
            'total' => $ticketToClose->calculateTotal(),
            'entry_time' => $ticketToClose->getEntryTime()->format('d/m/Y H:i'),
            'exit_time' => $ticketToClose->getExitTime()->format('d/m/Y H:i')
        ];
    }
}