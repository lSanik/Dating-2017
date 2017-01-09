<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'ticket_messages';

    protected $fillable = [
        'from', 'subjects',
        'subject', 'message', 'status',
    ];

    public function subjects()
    {
        return $this->hasOne('App\Models\TicketSubjects');
    }

    public function reply()
    {
        return $this->hasMany('App\Models\TicketReply');
    }

    public function media()
    {
        return $this->hasMany('App\Models\TicketMessageMedia');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function unreadCount()
    {
        if (\Auth::user()->hasRole('Owner') || \Auth::user()->hasRole('Moder')) {
            return self::where('status', '=', self::getStatus())->count();
        }else{
            return self::where('status', '=', self::getStatus())
                ->where('from', '=', \Auth::user()->id)
                ->count();
        }
    }

    public static function unread()
    {
        if (\Auth::user()->hasRole('Owner') || \Auth::user()->hasRole('Moder')) {
            return self::where('status', '=', self::getStatus())
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid')
                ->paginate(5);
        }else {
            return self::where('status', '=', self::getStatus())
                ->where('from', '=', \Auth::user()->id)
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid')
                ->paginate(5);
        }
    }

    private static function getStatus()
    {
        if (\Auth::user()->hasRole('Owner') || \Auth::user()->hasRole('Moder')) {
            return 0;
        }

        if (\Auth::user()->hasRole('Partner')) {
            return 1;
        }
    }
}
