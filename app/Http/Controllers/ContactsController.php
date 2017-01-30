<?php

namespace App\Http\Controllers;

use App\Models\ContactMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TicketController;

class ContactsController extends Controller
{
    public function show()
    {

        if(!Auth::user()){
            return view('client.contacts');
        }else {
            return redirect('/contacts/tickets');
        }
    }

    public function sendMessage(Request $request)
    {

        $rules = [
            'name'    => 'required',
            'email'   => 'required|email',
            'message' => 'required',
        ];

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        $message = new ContactMessages();
        $message->name = $request->input('name');
        $message->email = $request->input('email');
        $message->subject = $request->input('subject');
        $message->message = $request->input('message');
        $message->flag = 1;
        $message->save();

        //@todo send email to admin
        \Session::flash('success_message', trans('contacts.success'));

        return redirect(url('/'.LaravelLocalization::getCurrentLocale().'/contacts'));
    }

    public function showUnreaded()
    {
        $messages = ContactMessages::where('flag', '=', 1)->get();

        return view('admin.contacts.unread')->with([
           'messages'   => $messages,
        ]);
    }
}
