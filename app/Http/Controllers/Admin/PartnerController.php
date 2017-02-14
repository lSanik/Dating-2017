<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function __construct()
    {
        view()->share('new_ticket_messages', parent::getUnreadMessages());
        view()->share('unread_ticket_count', parent::getUnreadMessagesCount());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = new User();
        $users = $user->where('users.role_id', '=', 3)
                ->rightJoin('roles', 'users.role_id', '=', 'roles.id')
                ->select(['users.id', 'email', 'first_name', 'last_name', 'name'])
                ->paginate(15);

        return view('admin.profile.partners.index')->with([
            'users'     => $users,
            'heading'   => 'Все партнеры',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.profile.partners.create')->with([
            'heading' => 'Добавить партнера',

        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'first_name'    => 'required',
            'last_name'     => 'required',
            'email'         => 'required|unique:users',
            'phone'         => 'required|unique:users',
            'password'      => 'required',
        ];

        $this->validate($request, $rules);

        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput();
        }

        $fileName = '';

        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $fileName = time().'-'.$file->getClientOriginalName();
            $destination = public_path().'/uploads/admins';
            $file->move($destination, $fileName);
        }

        $u = new User();

        $u->email = $request->input('email');
        $u->password = bcrypt($request->input('password'));
        $u->phone = $request->input('phone');
        $u->first_name = $request->input('first_name');
        $u->last_name = $request->input('last_name');
        $u->role_id = 3;

        $u->avatar = $fileName;

        if (!empty($request->input('info'))) {
            $u->info = $request->input('info');
        }

        if (!empty($request->input('company'))) {
            $u->company_name = $request->input('company');
        }

        if (!empty($request->input('contacts'))) {
            $u->contacts = $request->input('contacts');
        }
        $u->address = $request->input('address');
        $u->save();

        return redirect('/admin/partners');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return view('admin.profile.partners.show')->with([
            'heading' => 'Пользователь',
            'user'    => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.profile.partners.edit')->with([
            'user'    => $user,
            'heading' => 'Редактировать пользователя',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!empty($request->input('first_name'))) {
            $user->first_name = $request->input('first_name');
        }

        if (!empty($request->input('last_name'))) {
            $user->last_name = $request->input('last_name');
        }

        if (!empty($request->input('company'))) {
            $user->company_name = $request->input('company');
        }

        if (!empty($request->input('info'))) {
            $user->info = $request->input('info');
        }

        if (!empty($request->input('contacts'))) {
            $user->contacts = $request->input('contacts');
        }

        if (!empty($request->input('phone'))) {
            $user->phone = $request->input('phone');
        }

        if (!empty($request->input('address'))) {
            $user->address = $request->input('address');
        }

        // Check email changes
        if ($request->input('email') != $user->email) {
            $user->email = $request->input('email');
        }

        // Check password changes
        if($request->input('password') !=null){
            $user->password   = bcrypt($request->input('password'));
        }

        //
        $user->address = $request->input('address');

        /* Check new file  */
        if (!empty($request->file())) {
            if (File::exists('/uploads/admins/'.$user->avatar)) {
                File::delete('/uploads/admins/'.$user->avatar);
            } // delete old file

            $file = $request->file('avatar');

            $fileName = time().'-'.$file->getClientOriginalName();
            $destination = public_path().'/uploads/admins';
            $file->move($destination, $fileName);

            $user->avatar = $fileName;
        }

        $user->save();

        return redirect('/admin/partners');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id', $id)->delete();
        \Session::flash('flash_success', 'Drop success!');

        return redirect('/admin/partners');
    }
}
