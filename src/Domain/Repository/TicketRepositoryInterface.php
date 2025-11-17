<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Ticket;

interface TicketRepositoryInterface
{
    public function save(Ticket $ticket): void;
    public function update(Ticket $ticket): void;
    /** @return Ticket[] */
    public function findAll(): array;
    public function clear(): void; 
}