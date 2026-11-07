<?php

namespace App\Enums;

enum TicketStatus : string {
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';
    case RESOLVED = 'resolved';

    // Method to check if the ticket can transition to a new status
    public function canTransitionTo(TicketStatus $target): bool
    {
        return match ($this) {
            self::OPEN => in_array($target, [self::IN_PROGRESS]),
            self::IN_PROGRESS => in_array($target, [self::RESOLVED, self::OPEN]),
            self::RESOLVED => in_array($target, [self::CLOSED, self::IN_PROGRESS]),
            self::CLOSED => false, // Terminal state
        };
    }
}
