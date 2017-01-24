<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    protected $table = 'ticket_statuses';

    protected $fillable = [
        'name'
    ];
    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket', 'ticket_status_id');
    }
}
