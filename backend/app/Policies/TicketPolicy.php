<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::SUPPORT_AGENT]) || $ticket->requester_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return false;
    }

    public function assign(User $user, Ticket $ticket) : bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::SUPPORT_AGENT]);
    }

    public function resolve(User $user, Ticket $ticket) : bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::SUPPORT_AGENT]);
    }

    public function close(User $user, Ticket $ticket) : bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::SUPPORT_AGENT]);
    }

    public function addComment(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::SUPPORT_AGENT]) || $ticket->requester_id === $user->id;
    }
}
