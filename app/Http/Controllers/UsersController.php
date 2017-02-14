<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Country;
use App\Models\Messages;
use App\Models\Profile;
use App\Models\Session;
use App\Models\Smiles;
use App\Models\State;
use App\Models\User;
use App\Services\ZodiacSignService;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Profile
     */
    private $profile;

    /**
     * @var ZodiacSignService
     */
    private $zodiacSignService;

    public function __construct(User $user, Profile $profile, ZodiacSignService $zodiacSignService)
    {
        $this->user = $user;
        $this->profile = $profile;
        $this->zodiacSignService = $zodiacSignService;
        parent::__construct();
    }

    public function show($id)
    {
        $user = User::select([
            'users.id as uid',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.avatar',
                'users.webcam',
                'users.city_id',
                'users.country_id',
                'profile.*',
                'countries.name as country',
                'countries.name_en as country_en',
                'cities.name as city',
                'cities.name_en as city_en',
            ])
            ->join('profile', 'profile.user_id', '=', 'users.id')
            ->leftjoin('countries', 'countries.id', '=', 'users.country_id')
            ->leftJoin('cities', 'cities.id', '=', 'users.city_id')
            ->where('users.id', '=', $id)
            ->first();

        $albums = Album::where('user_id', '=', $id)->get();

        return view('client.profile.show')->with([
            'u' => $user,
            'id' => $id,
            'albums' => $albums,
            'sign'  => $this->zodiacSignService->getSignByBirthday($user->birthday),
        ]); 
    }

    /**
     * Show the form for editing the profile.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        if(\Auth::user()->id == $id){
            $selects = [
                'eyes'       => $this->profile->getEnum('eyes'),
                'hair'      => $this->profile->getEnum('hair'),
                'education' => $this->profile->getEnum('education'),
                'kids'      => $this->profile->getEnum('kids'),
                'kids_live' => $this->profile->getEnum('kids_live'),
                'want_kids' => $this->profile->getEnum('want_kids'),
                'family'    => $this->profile->getEnum('family'),
                'religion'  => $this->profile->getEnum('religion'),
                'smoke'     => $this->profile->getEnum('smoke'),
                'drink'     => $this->profile->getEnum('drink'),
                'finance_income'     => $this->profile->getEnum('finance_income'),
                'english_level'     => $this->profile->getEnum('english_level'),
            ];



            return view('client.profile.edit')->with([
                'user'      => $this->user->find($id),
                'selects'   => $selects,
                'countries' => Country::all_order(),
                'id'        => $id,
            ]);
        } else
            return redirect('/'.\App::getLocale().'/profile/show/'.$id);
    }

    public function online()
    {
        if (\Auth::user()->hasRole('male')) {
            $users = User::where('role_id', '=', 5)->paginate(20);
        } else {
            $users = User::where('role_id', '=', 4)->paginate(20);
        }

        return view('client.profile.users')->with([
           'users' => $users,
        ]);
    }

    /**
     * Show the users photo albums and editing actions.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function profilePhoto(int $id)
    {
        $albums = Album::where('user_id', '=', $id)->get();

        return view('client.profile.photos')->with([
            'albums' => $albums,
            'id' => $id,
        ]);
    }

    /**
     * Show the users videos and editing actions.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function profileVideo(int $id)
    {
        return view('client.profile.video')->with([

        ]);
    }

    /**
     * Show users income messages.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function profileMail(int $id)
    {
        $from = \DB::table('messages')
            ->select('messages.id as mid', 'messages.message', 'users.id as uid', 'users.first_name', 'users.avatar')
            ->join('users', 'users.id', '=', 'messages.to_user')
            ->where('messages.from_user', '=', \Auth::user()->id)
            ->paginate(30);

        $to = \DB::table('messages')
            ->select('messages.id as mid', 'messages.message', 'users.id as uid', 'users.first_name', 'users.avatar')
            ->join('users', 'users.id', '=', 'messages.from_user')
            ->where('messages.to_user', '=', \Auth::user()->id)
            ->paginate(30);

        return view('client.profile.mail')->with([
            'from'   =>  $from,
            'to'    => $to
        ]);
    }

    /**
     * Show users income smiles.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function profileSmiles(int $id)
    {
        $smiles = \DB::table('smiles')
            ->select('users.id', 'users.first_name')
            ->join('users', 'users.id', '=', 'smiles.from')
            ->where('smiles.to', '=', $id)
            ->get();

        return view('client.profile.smiles')->with([
            'smiles' => $smiles
        ]);
    }

    /**
     * Show users income gifts.
     *
     * @param int $id
     *
     * @return mixed1
     */
    public function profileGifts($id)
    {
        return view('client.profile.gifts')->with([

        ]);
    }

    /**
     * Show users finance statistic.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function profileFinance($id)
    {
        return view('client.profile.finance')->with([

        ]);
    }

    /**
     * Update the user profile in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            //'avatar' => 'required',
            'first_name' => 'required',
            'second_name'  => 'required',
            'email'      => 'required',
        ]);

     //   dump($request->input());

        $user = User::find($id);

        if ( $request->file('avatar') ) {
            if ($request->file('avatar')) {
                $file = $request->file('avatar');
                $user_avatar = time().'-'.$file->getClientOriginalName();
                $destination = public_path().'/uploads';
                $file->move($destination, $user_avatar);
            }
            $user->avatar = $user_avatar;
        }
/*
 *
           if ($request->file('avatar')) {
                $file = $request->file('avatar');
                $user_avatar = time().'-'.$file->getClientOriginalName();
                $destination = public_path().'/uploads/girls/avatars';
                $file->move($destination, $user_avatar);
            }
 *
 */
        $user->first_name = $request->input('first_name');
        $user->last_name  = $request->input('second_name');
        $user->email      = $request->input('email');

        if(!empty($request->input('password')))
            $user->password   = bcrypt( $request->input('password'));

        $user->phone      = $request->input('phone');
        $user->country_id = $request->input('country');
        $user->state_id   = $request->input('state');
        $user->city_id    = $request->input('city');

        $user->save();
        if( empty((Profile::where('user_id', '=', $id)->first())) ){
            $profile = new Profile();
            $profile->user_id   = $id;
            $profile->birthday   = new \DateTime($request->input('birthday'));  //check age new \DateTime($request->input('birthday'));
            $profile->height    = $request->input('height');
            $profile->weight   = $request->input('weight');
            $profile->eyes       = $request->input('eyes');
            $profile->hair      = $request->input('hair');
            $profile->education = $request->input('education');
            $profile->kids      = $request->input('kids');
            $profile->kids_live      = $request->input('kids_live');
            $profile->want_kids = $request->input('want_kids');
            $profile->family    = $request->input('family');
            $profile->religion  = $request->input('religion');
            $profile->smoke     = $request->input('smoke');
            $profile->drink     = $request->input('drink');
            $profile->occupation= $request->input('occupation');
            $profile->about     = $request->input('about');
            $profile->looking   = $request->input('looking');
            $profile->l_age_start   = $request->input('l_age_start');
            $profile->l_age_stop    = $request->input('l_age_stop');
            $profile->finance_income   = $request->input('finance_income');
            $profile->english_level    = $request->input('english_level');
            $profile->save();
            return redirect('/profile/show/'.$id);
        } else {
            $profile = Profile::where('user_id', '=', $id)->first();
            $profile->user_id   = $id;
            $profile->birthday  = new \DateTime($request->input('birthday'));  //check age new \DateTime($request->input('birthday'));
            $profile->height    = $request->input('height');
            $profile->weight    = $request->input('weight');
            $profile->eyes       = $request->input('eyes');
            $profile->hair      = $request->input('hair');
            $profile->education = $request->input('education');
            $profile->kids      = $request->input('kids');
            $profile->kids_live = $request->input('kids_live');
            $profile->want_kids = $request->input('want_kids');
            $profile->family    = $request->input('family');
            $profile->religion  = $request->input('religion');
            $profile->smoke     = $request->input('smoke');
            $profile->drink     = $request->input('drink');
            $profile->occupation =$request->input('occupation');
            $profile->about     = $request->input('about');
            $profile->looking   = $request->input('looking');
            $profile->l_age_start = $request->input('l_age_start');
            $profile->l_age_stop = $request->input('l_age_stop');
            $profile->finance_income   = $request->input('finance_income');
            $profile->english_level    = $request->input('english_level');
            $profile->save();
            return redirect('/profile/show/'.$id);
        }
    }
}
