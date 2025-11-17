<?php

namespace App\Infra\Repository;

use App\Domain\Entity\Car;
use App\Domain\Entity\Motorcycle;
use App\Domain\Entity\Ticket;
use App\Domain\Entity\Truck;
use App\Domain\Repository\TicketRepositoryInterface;
use App\Infra\Database\Connection;
use DateTime;
use PDO;

class SqliteTicketRepository implements TicketRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::get();
    }

    public function save(Ticket $ticket): void
    {
        $vehicleType = (new \ReflectionClass($ticket->getVehicle()))->getShortName();

        $stmt = $this->pdo->prepare("
            INSERT INTO tickets (plate, vehicle_type, entry_time) 
            VALUES (:plate, :type, :entry)
        ");
        
        $stmt->bindValue(':plate', $ticket->getVehicle()->getPlate());
        $stmt->bindValue(':type', $vehicleType);
        $stmt->bindValue(':entry', $ticket->getEntryTime()->format('Y-m-d H:i:s'));
        
        $stmt->execute();
    }

    public function update(Ticket $ticket): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE tickets SET exit_time = :exit_time WHERE plate = :plate AND exit_time IS NULL
        ");

        $stmt->bindValue(':exit_time', $ticket->getExitTime()?->format('Y-m-d H:i:s'));
        $stmt->bindValue(':plate', $ticket->getVehicle()->getPlate());

        $stmt->execute();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM tickets");
        $ticketsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $tickets = [];

        foreach ($ticketsData as $data) {
            $vehicle = match ($data['vehicle_type']) {
                'Car' => new Car($data['plate']),
                'Motorcycle' => new Motorcycle($data['plate']),
                'Truck' => new Truck($data['plate']),
                default => throw new \Exception("Tipo desconhecido")
            };

            $ticket = new Ticket(
                $data['id'],
                $vehicle,
                new DateTime($data['entry_time'])
            );

            if ($data['exit_time']) {
                $ref = new \ReflectionClass($ticket);
                $prop = $ref->getProperty('exitTime');
                $prop->setValue($ticket, new DateTime($data['exit_time']));
            }

            $tickets[] = $ticket;
        }

        return $tickets;
    }

    public function clear(): void
    {
        $this->pdo->exec("DELETE FROM tickets");
        $this->pdo->exec("DELETE FROM sqlite_sequence WHERE name='tickets'");
    }
}