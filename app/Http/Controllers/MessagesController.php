<?php

namespace App\Http\Controllers;


use App\Constants;
use App\Models\Expenses;
use App\Models\ServicesPrice;
use App\Models\User;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use App\Models\Messages;

use App\Models\ChatContactList;

use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    /**
     * @var
     */
    private $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;

        parent::__construct();
    }

    public function index($id=19){

        $from_user = \DB::table('messages')
            ->select('message', 'messages.created_at as time', 'users.first_name as name', 'users.avatar as ava')
            ->join('users', 'users.id', '=', 'messages.from_user')
            ->where('messages.from_user', '=', $id)
            ->where('messages.to_user', '=', \Auth::user()->id)
            ->orderBy('messages.created_at', 'asc')
            ->get();

        $to_user =  \DB::table('messages')
            ->select('message', 'messages.created_at as time', 'users.first_name as name', 'users.avatar as ava')
            ->join('users', 'users.id', '=', 'messages.from_user')
            ->where('messages.from_user', '=', \Auth::user()->id)
            ->where('messages.to_user', '=', $id)
            ->orderBy('messages.created_at', 'asc')
            ->get();
        
        $messages = array_merge($from_user, $to_user);

        $user = User::find(\Auth::user()->id);
        $contact_lists = ChatContactList::where('user_id','=',\Auth::user()->id)->get();
        $user_contact_list_data=array();
        foreach ($contact_lists as $contact_list){
            $user_contact_list_data[]=[
                'contact_info'=> $contact_list,
                'user_data'=> User::getUserShtInfo($contact_list['contact_id']),

            ];
        }
        if (!\Auth::user()
            || \Auth::user()->hasRole('Male')
            || \Auth::user()->hasRole('Alien')
        ){
            $users = $this->getUsers(5);
        } else {
            $users = $this->getUsers(4);
        }


        usort($messages, [$this, 'cmp_time']);

        return view('client.profile.messages')->with([
            'messages' => $messages,
            'to' => $id,
            'user'  => $user,
            'users' => $users,
            'user_contact_list_data' =>$user_contact_list_data
        ]);

    }

    public function cmp_time($a, $b)
    {
        if($a->time > $b->time)
            return 1;
    }

    public function send(Request $request, $id)
    {

        $rules = [
            'to'        => 'required',
            'from'      => 'required',
            'message'   => 'required',
        ];

        $v = Validator::make($request->all(), $rules);

        if($v->fails())
        {
            return redirect()->back()->withErrors($v->errors());
        }
/* @TODO: Допилить поправить.
        if( \Auth::user()->hasRole('male') ){

            if (!((float) $this->getMoney() >= (float) $this->expenseService->getCost(Constants::EXP_FLP))){
                \Session::flash('flash-warning', trans('payments.enoughMoney'));
                return redirect()->back();

            } else {
                $this->expenseService->setExpense(
                    \Auth::user()->id,
                    $request->input('to'),
                    Constants::EXP_MESSAGE,
                    $this->expenseService->getCost(Constants::EXP_MESSAGE)
                );
            }
        }
        dump('heek');
*/
        //if($request->input('to') == $id){ @TODO: ЧТО ТЫ ТУТ ХОТЕЛ СДЕЛАТЬ!?

            $message = new Messages();
            $message->from_user = $request->input('from');
            $message->to_user   = $request->input('to');
            $message->message   = $this->robot( $request->input('message') );
            $message->save();

            return redirect()->back();
        //}

        //return redirect()->back();
    }


}
