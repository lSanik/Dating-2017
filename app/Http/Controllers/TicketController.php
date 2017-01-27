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
    public function index(){

        $tickets = DB::table('ticket_messages')
            ->where('from', '=', Auth::id())
            ->orderBy('updated_at', 'DESC')
            ->leftJoin('ticket_statuses', 'ticket_messages.ticket_status_id', '=', 'ticket_statuses.id')
            ->leftJoin('users', 'ticket_messages.from', '=', 'users.id')
            ->select('ticket_messages.*', 'ticket_statuses.name',
                     'users.first_name', 'users.avatar')
            ->paginate(8);

        $subjects = DB::table('ticket_subjects')
            ->orderBy('id', 'ASC')
            ->select('name')
            ->get();


        return view('client.contacts')->with([
            'tickets' => $tickets,
            'subjects' => $subjects,
            'this_r' => $this,
        ]);

    }

    public function createTicket(){
        //@todo Validation


    }

    public function createReply(Request $request, $id){
        //@todo check Validation
        $this->validate($request, [
            'reply' => 'required',
        ]);

        $reply = TicketReply::find($id);

        $reply->message_id = $id;
        $reply->reply = $request->input('reply');
        $reply->r_uid = Auth::id();

        $reply->save();
        //@todo глюк с отображением диалога, лишние админы
        //@todo add Session::flash()
        return redirect('contacts/tickets');
    }

    public function closeTicket($id){

        \Session::flash('success_message', 'ID:'.$id.' '.trans('contacts.ticket_closed'));

          DB::table('ticket_messages')
             ->where('id', $id)
             ->update(['ticket_status_id' => 3]);

        return redirect('contacts/tickets');
    }

    public function getReply($ticket_id){
        return DB::table('ticket_reply')
            ->where('message_id', '=', $ticket_id)
            ->orderBy('updated_at', 'ASC')
            ->leftJoin('users', 'ticket_reply.r_uid', '=', 'users.id')
            ->select('ticket_reply.*', 'users.first_name', 'users.avatar')
            ->get();
    }
}
