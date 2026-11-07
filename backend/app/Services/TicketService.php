<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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

    public function assignTicket(Ticket $ticket, User $assignee, User $actor): Ticket
    {
        return DB::transaction(function () use ($ticket, $assignee, $actor) {

            // Step 1: Check if the ticket is already close
            if($ticket->status === TicketStatus::CLOSED)
            {
                throw new \DomainException("Cannot assign a close ticket.");
            }

            // Step 2: Check If the new assignee is the same as the old assignee, don't create an activity
            if ($assignee->id === $ticket->assignee_id) {
                return $ticket;
            }

            // Step 3: Capture old assignee name before updating
            $oldAssigneeName  = $ticket->assignee?->name ?? 'Unassigned';

            // Step 4: Update ticket assignee and  Check if status should auto-advance from OPEN -> IN_PROGRESS
            $ticket->assignee_id = $assignee->id;
            if($ticket->status === TicketStatus::OPEN)
            {
                $ticket->status = TicketStatus::IN_PROGRESS;
            }

            // Step 5: save the changes
            $ticket->save();

            // Step 6: create activity record
            $ticket->activities()->create([
                'user_id' => $actor->id,
                'event_type' => 'TICKET_ASSIGNED',
                'old_value' => $oldAssigneeName,
                'new_value' => $assignee->name,
                'description' => "Assigned to {$assignee->name}"
            ]);

            return $ticket;
        });
    }

    public function resolveTicket(Ticket $ticket, string $notes, User $actor): Ticket
    {
        return DB::transaction(function () use ($ticket, $notes, $actor) {
            // Step 1: check if the ticket can transition to resolved
            $isTicketResolved = $ticket->status->canTransitionTo(TicketStatus::RESOLVED);
            if(!$isTicketResolved)
            {
                throw new \DomainException("Cannot resolve a ticket that is currently {$ticket->status->value}");
            }
            // Get Old Status
            $oldStatus = $ticket->status->value;

            // Step 2: Update Ticket Status
            $ticket->status = TicketStatus::RESOLVED;
            $ticket->resolution_notes = $notes;
            $ticket->resolved_at = now();
            $ticket->save();

            // Step 3: create activity
            $ticket->activities()->create([
                'event_type' => 'TICKET_RESOLVED',
                'user_id' => $actor->id,
                'old_value' => $oldStatus,
                'new_value' => TicketStatus::RESOLVED->value,
                'description' => "Ticket marked as resolved: {$notes}"
            ]);

            return $ticket;
        });
    }

    public function closeTicket(Ticket $ticket, User $actor): Ticket
    {
        return DB::transaction(function () use ($ticket, $actor) {
            // Step 1: check if the ticket can transition to resolved
            $isTicketClosed = $ticket->status->canTransitionTo(TicketStatus::CLOSED);
            if(!$isTicketClosed)
            {
                throw new \DomainException("Cannot close a ticket that is currently {$ticket->status->value}");
            }
            // Get Old Status
            $oldStatus = $ticket->status->value;

            // Step 2: Update Ticket Status
            $ticket->status = TicketStatus::CLOSED;
            $ticket->closed_at = now();
            $ticket->save();

            // Step 3: create activity
            $ticket->activities()->create([
                'event_type' => 'TICKET_CLOSED',
                'user_id' => $actor->id,
                'old_value' => $oldStatus,
                'new_value' => TicketStatus::CLOSED->value,
                'description' => "Ticket closed"
            ]);

            return $ticket;
        });
    }

    public function addComment(Ticket $ticket, string $body, User $actor): TicketComment
    {
        if($ticket->status === TicketStatus::CLOSED)
        {
            throw new \DomainException("Cannot add comments to a closed ticket.");
        }

        return DB::transaction(function () use ($ticket, $body, $actor) {
            // Step 1: create comment
            $comment = $ticket->comments()->create([
                'user_id' => $actor->id,
                'body'    => $body,
            ]);

            $ticket->activities()->create([
                'event_type' => 'COMMENT_ADDED',
                'user_id' => $actor->id,
                'old_value' => null,
                'new_value' => null,
                'description' => "Comment added by {$actor->name}"
            ]);

            return $comment;
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
