<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function createTicket(array $data, User $requester): Ticket
    {
        return DB::transaction(function () use ($data, $requester) {
            $ticket = Ticket::create([
                'ticket_number' => $this->generateTicketNumber(),
                'requester_id' => $requester->id,
                'assignee_id' => $data['assignee_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'status' => 'open',
                'priority' => $data['priority'] ?? 'medium',
            ]);

            $ticket->activities()->create([
                'user_id' => $requester->id,
                'event_type' => 'TICKET_CREATED',
                'description' => 'Ticket was opened.',
            ]);

            return $ticket;
        });
    }

    private function generateTicketNumber(): string
    {
        $prefix = 'TCK';
        $date = date('Ym');
        $pattern = "{$prefix}-{$date}-%";

        // Look for the latest ticket created in the CURRENT month
        $lastTicket = Ticket::where('ticket_number', 'like', $pattern)
            ->orderBy('id', 'desc')
            ->first();

        // Extract the last 4 digits (e.g., '0042' -> 42), or start at 0 if new month
        $lastNumber = $lastTicket ? (int) substr($lastTicket->ticket_number, -4) : 0;
        $nextNumber = $lastNumber + 1;
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$formattedNumber}";
    }
}
