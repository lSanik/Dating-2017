<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketSubjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TicketController extends Controller
{
    /* Вторая очередь, не обязательная доработка:
    упростить код, чтобы все статусы тикетов брались из таблицы ticket_statuses
    Сейчас система статусов дублируется: для пользователя отдельная, для админа - отдельная
    Алгоритм:
    Мужчина/Женщина/Alien
    создано новый тикет - status - 1 (requested)
    ответил - 2 (requested)
    закрыт - 3 (closed)
    Owner/Moderator/Parther
    создано новый тикет - status - 1 (requested)
    ответил - 2 (answered) / 1 (requested)
    закрыт - 3 (closed)
    */
    private $ticket;
    private $ts;

    public function __construct(Ticket $ticket, TicketSubjects $ts)
    {
        $this->middleware('auth');
        Auth::user()->hasRole(['Owner', 'Moder', 'Partner']);

        $this->ticket = $ticket;
        $this->ts = $ts;

        view()->share('new_ticket_messages', parent::getUnreadMessages());
        view()->share('unread_ticket_count', parent::getUnreadMessagesCount());
    }

    public function index()
    {
        if (\Auth::user()->hasRole('Owner') || \Auth::user()->hasRole('Moder')) {
            $tickets = \DB::table('ticket_messages')->where('status', '=', '0')
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid', 'users.avatar')
                ->get();
        }
        else {
            /* Получаем список id девушек, привлеченных партнером, в виде массива - $ids */
            $partner_girls=User::GirlsByPartner(\Auth::user()->id);
            $ids = [];
            foreach($partner_girls as $pg){
                $ids[] = $pg->id;
            }
            $ids[] = \Auth::user()->id;
            /* Делаем выборку тикетов по списку id */
            $tickets = \DB::table('ticket_messages')->whereIn('from', $ids)
                ->where('status', '!=', '2')
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid', 'users.avatar')
                ->paginate(10);
        }

        return view('admin.ticket.all')->with([
            'heading' => 'Новое сообещние администратору/модератору',
            'tickets' => $tickets,
        ]);
    }

    public function answered()
    {
        if (\Auth::user()->hasRole('Owner') || \Auth::user()->hasRole('Moder')) {
            $tickets = \DB::table('ticket_messages')->where('status', '=', '1')
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid', 'users.avatar')
                ->get();
        } else {
            /* Получаем список id девушек, привлеченных партнером, в виде массива - $ids */
            $partner_girls=User::GirlsByPartner(\Auth::user()->id);
            $ids = [];
            foreach($partner_girls as $pg){
                $ids[] = $pg->id;
            }
            $ids[] = \Auth::user()->id;
            /* Делаем выборку тикетов по списку id */
            $tickets = \DB::table('ticket_messages')->whereIn('from', $ids)
                ->where('status', '=', '0')
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid', 'users.avatar')
                ->paginate(10);
        }

        return view('admin.ticket.all')->with([
            'heading' => 'Новое сообещние администратору/модератору',
            'tickets' => $tickets,
        ]);
    }

    public function closed()
    {
        if (!\Auth::user()->hasRole('Owner') || !\Auth::user()->hasRole('Moder')) {
            $tickets = \DB::table('ticket_messages')
                ->where('from', '=', \Auth::user()->id)
                ->where('status', '=', '2')
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid', 'users.avatar')
                ->get();
        } else {
            /* Получаем список id девушек, привлеченных партнером, в виде массива - $ids */
            $partner_girls=User::GirlsByPartner(\Auth::user()->id);
            $ids = [];
            foreach($partner_girls as $pg){
                $ids[] = $pg->id;
            }
            $ids[] = \Auth::user()->id;
            /* Делаем выборку тикетов по списку id */
            $tickets = \DB::table('ticket_messages')->whereIn('from', $ids)
                ->where('status', '=', '2')
                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                ->join('users', 'ticket_messages.from', '=', 'users.id')
                ->select('ticket_messages.*', 'ticket_subjects.name',
                    'users.first_name', 'users.last_name', 'users.id as uid', 'users.avatar')
                ->get();
        }

        return view('admin.ticket.all')->with([
            'heading' => 'Новое сообещние администратору/модератору',
            'tickets' => $tickets,
        ]);
    }


    public function show($id)
    {

        $ticket = $this->ticket->where('ticket_messages.id', '=', $id)
                                ->join('ticket_subjects', 'ticket_messages.subjects', '=', 'ticket_subjects.id')
                                ->join('users', 'ticket_messages.from', '=', 'users.id')
                                ->select('ticket_messages.*', 'ticket_subjects.name',
                                    'users.first_name', 'users.last_name', 'users.id as uid', 'users.avatar')
                                ->get();

        $reply = TicketReply::where('message_id', '=', $id)
                                ->join('users', 'ticket_reply.r_uid', '=', 'users.id')
                                ->select('users.first_name', 'users.last_name', 'ticket_reply.*')
                                ->get();
        return view('admin.ticket.show')->with([
            'heading' => 'Ticket #'.$id,
            'tickets' => $ticket,
            'reply'   => $reply,
        ]);
    }

    public function close($id)
    {
        $ticket = $this->ticket->find($id);
        $ticket->status = 2;
        $ticket->ticket_status_id = 3;

        $ticket->save();

        \Session::flash('flash_success', 'Тикет закрыт');

        return redirect(\App::getLocale().'/admin/support');
    }

    public function newTicket()
    {
        $selects = $this->ts->all();

        return view('admin.ticket.new')->with([
            'heading' => 'Новое сообщениее',
            'selects' => $selects,

        ]);
    }

    public function create(Request $request)
    {
        $this->ticket->from = $request->user()->id;
        $this->ticket->subjects = $request->input('subjects');
        $this->ticket->subject = $request->input('subject');
        $this->ticket->message = $request->input('message');
        $this->ticket->status = 0;
        $this->ticket_status_id = 1;


        if ( $request->file('download_file') ) {
            if ($request->file('download_file')) {
                $file = $request->file('download_file');
                $user_file = time().'-'.$file->getClientOriginalName();
                $destination = public_path().'/uploads/user_files';
                $file->move($destination, $user_file);
            }
            $this->ticket->download_file = $user_file;
        }
        $this->ticket->save();

        return redirect(\App::getLocale().'/admin/support');
    }

    public function answer(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if($request->user()->hasRole(['Man', 'Woman', 'Alien'])){
            $ticket->status = 1;
        } else {
            $ticket->status = ($ticket->status ==1) ? 0 : 1;
        }
        if($request->user()->hasRole(['Man', 'Woman', 'Alien'])){
            $ticket->ticket_status_id = 2;
        } else {
            $ticket->ticket_status_id = ($ticket->status ==2) ? 1 : 2;
        }
        $ticket->save();

        $reply = new TicketReply();
        $reply->message_id = $id;
        $reply->reply = $request->input('reply');
        $reply->r_uid = $request->user()->id;

        if ( $request->file('download_file') ) {
            if ($request->file('download_file')) {
                $file = $request->file('download_file');
                $user_file = time().'-'.$file->getClientOriginalName();
                $destination = public_path().'/uploads/user_files';
                $file->move($destination, $user_file);
            }
            $reply->download_file = $user_file;
        }

        $reply->save();

        \Session::flash('flash_success', 'Ответ добавлен');

        return redirect(\App::getLocale().'/admin/support/show/'.$id);
    }
}
