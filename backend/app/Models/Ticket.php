<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'requester_id',
        'assignee_id',
        'category_id',
        'subject',
        'description',
        'status',
        'priority',
        'resolution_notes',
        'resolved_at',
        'closed_at',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    // cast
    protected function casts() : array
    {
        return [
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
        ];
    }
}
