<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketSubjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**/
    public function index(){

        $tickets = DB::table('ticket_messages')
            ->where('from', '=', Auth::id())
            ->orderBy('updated_at', 'DESC')
            ->leftJoin('ticket_statuses', 'ticket_messages.ticket_status_id', '=', 'ticket_statuses.id')
            ->select('ticket_messages.id', 'ticket_messages.subject', 'ticket_messages.updated_at', 'ticket_statuses.name')
            ->paginate(8);


        return view('client.contacts')->with([
            'tickets' => $tickets,
        ]);

    }
}
