<?php

namespace App\Application\UseCase;

use App\Domain\Repository\TicketRepositoryInterface;

class GetDashboard
{
    public function __construct(
        private TicketRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        $tickets = $this->repository->findAll();

        $stats = [
            'total_vehicles' => 0,
            'total_revenue' => 0.0,
            'active_vehicles' => []
        ];

        foreach ($tickets as $ticket) {
            $stats['total_vehicles']++;
            
            if ($ticket->getExitTime() !== null) {
                $stats['total_revenue'] += $ticket->calculateTotal();
            } else {
                $type = (new \ReflectionClass($ticket->getVehicle()))->getShortName();
                $stats['active_vehicles'][] = [
                    'plate' => $ticket->getVehicle()->getPlate(),
                    'type' => $type,
                    'entry' => $ticket->getEntryTime()->format('H:i')
                ];
            }
        }

        return $stats;
    }
}