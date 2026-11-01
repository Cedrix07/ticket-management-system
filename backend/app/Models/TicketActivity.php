<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketActivity extends Model
{
    protected $table = 'ticket_activities';
    const UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'event_type',
        'old_value',
        'new_value',
        'description',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
