<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Passport;
use App\Models\Profile;
use App\Models\ProfileMedia;
use App\Models\State;
use App\Models\Status;
use App\Models\User;
use App\Models\Why;
use App\Models\profileImages;

use App\Models\Album;
use App\Models\Images;

use App\Services\ZodiacSignService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//@todo refactor this shit
//@todo check email before add

class GirlsController extends Controller
{
    private $user;
    private $profile;
    private $profileImages;
    private $passport;
    private $passport_photos = [];

    public function __construct(User $user, Profile $profile, Passport $passport,Album $album,profileImages $profileImages)
    {
        $this->middleware('auth');

        $this->user = $user;
        $this->Album = $album;
        $this->profile = $profile;
        $this->passport = $passport;
        $this->profileImages=$profileImages;
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
        if (Auth::user()->hasRole('Owner') || Auth::user()->hasRole('Moder')) {
            $partners = User::where('role_id', '=', '3')->get();
            $girls = User::where('role_id', '=', '5')->get();
        } elseif (Auth::user()->hasRole('Partner')) {

            $girls = User::where('role_id', '=', '5')
                                ->where('partner_id', '=', Auth::user()->id)
                                ->get();
        }

        return view('admin.profile.girls.index')->with([
            'heading' => 'Все девушки',
            'girls'   => $girls,
            'partners'=> $partners,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $selects = [
            'gender'    => $this->profile->getEnum('gender'),
            'eyes'       => $this->profile->getEnum('eyes'),
            'hair'      => $this->profile->getEnum('hair'),
            'education' => $this->profile->getEnum('education'),
            'kids'      => $this->profile->getEnum('kids'),
            'kids_live' => $this->profile->getEnum('kids_live'),
            'want_kids'    => $this->profile->getEnum('want_kids'),
            'family'    => $this->profile->getEnum('family'),
            'religion'  => $this->profile->getEnum('religion'),
            'smoke'     => $this->profile->getEnum('smoke'),
            'drink'     => $this->profile->getEnum('drink'),
            'finance_income' => $this->profile->getEnum('finance_income'),
            'english_level' => $this->profile->getEnum('english_level'),
        ];
        $countries = Country::orderBy('name')->get();
        //$countries = Country::all();

        return view('admin.profile.girls.create')->with([
            'heading'   => 'Добавить девушку',
            'selects'   => $selects,
            'countries' => $countries,
            'zodiac_list'=>ZodiacSignService::getAll(),
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
        $this->validate($request, [
            'first_name'    => 'required|max:255',
            'second_name'   => 'required|max:255',
            'b_year'        => 'required',
            'b_month'       => 'required',
            'b_day'         => 'required',
            'email'         => 'required|max:128',
            'phone'         => 'required|max:20',
            'password'      => 'required',
            'country'        => 'required',
            'state'         => 'required',
            'city'          => 'required',
            'passno'        => 'required',
            'pass_photo'    => 'required',
            'b_year_pasp'        => 'required',
            'b_month_pasp'       => 'required',
            'b_day_pasp'         => 'required',
            'height'        => 'numeric',
            'weight'        => 'numeric',
        ]);


        $check = $this->passport->where('passno', 'like', str_replace(' ', '', $request->input('passno')))->first();

        if (!$check) {
            if ($this->age(date('d/m/Y',strtotime($request->input('b_day').'-'.$request->input('b_month').'-'.$request->input('b_year')))) < 18) {
                \Session::flash('flash_error', 'Девушка младше 18');
                return redirect(\App::getLocale().'/admin/girl/new');
            }

            $user_avatar = 'empty_girl.png';

            if ($request->file('avatar')) {
                $user_avatar = $this->upload($request->file('avatar'));
            }
            $user_passoprt = $this->upload($request->file('pass_photo'));
            /*
             * Create user with role female/male
             *
             */

            $this->user->avatar = $user_avatar;
            $this->user->webcam = $request->input('webcam') ? 1 : 0;
            $this->user->hot = $request->input('hot') ? 1 : 0;
            $this->user->first_name = $request->input('first_name');
            $this->user->last_name = $request->input('second_name');
            $this->user->email = $request->input('email');
            $this->user->phone = $request->input('phone');
            $this->user->password = bcrypt($request->input('password'));

            $this->user->country_id = $request->input('country');
            $this->user->state_id = $request->input('state');
            $this->user->city_id = $request->input('city');

            $this->user->partner_id = Auth::user()->id;

            $gender = $request->input('gender');

            /* Проверка пола учасника */
            if ($gender == 'female') {
                $this->user->role_id = 5;
                $this->user->status_id = 5;
            } else {
                $this->user->role_id = 4;
                $this->user->status_id = 1;
            }

            $this->user->save();

            /*
             *  Add girl passport
             */

            $this->passport->user_id = $this->user->id;
            $this->passport->cover=$user_passoprt;
            $this->passport->date = str_replace(' ', '', date('Y-m-d',strtotime($request->input('b_day_pasp').'-'.$request->input('b_month_pasp').'-'.$request->input('b_year_pasp'))));
            $this->passport->passno = str_replace(' ', '', $request->input('passno'));
            $this->passport->save();

            /*
             * Create girl profile
             */
            if($request->file("profile_photo")!==null){
                foreach ($request->file("profile_photo") as $p_image){
                    $profile_image = new profileImages();
                    $profile_image->url=$this->upload(($p_image));
                    $profile_image->user_id=$this->user->id;
                    $profile_image->save();
                }
            }

            /*
            * Create girl profile
            */
            $this->profile->user_id = $this->user->id;
            $this->profile->birthday = date('Y-m-d',strtotime($request->input('b_day').'-'.$request->input('b_month').'-'.$request->input('b_year')));
            $this->profile->gender    = $request->input('gender');
            $this->profile->height    = $request->input('height');
            $this->profile->weight    = $request->input('weight');
            $this->profile->eyes       = $request->input('eyes');
            $this->profile->hair      = $request->input('hair');
            $this->profile->education = $request->input('education');
            $this->profile->kids      = $request->input('kids');
            $this->profile->want_kids = $request->input('want_kids');
            $this->profile->family    = $request->input('family');
            $this->profile->religion  = $request->input('religion');
            $this->profile->smoke     = $request->input('smoke');
            $this->profile->drink     = $request->input('drink');
            $this->profile->occupation= $request->input('occupation');
            $this->profile->kids_live = $request->input('kids_live');
            $this->profile->finance_income =$request->input('finance_income');
            $this->profile->about = $request->input('about');
            $this->profile->know_lang=$request->input('know_lang');
            $this->profile->english_level=$request->input('english_level');
            $this->profile->looking = $request->input('looking');
            $this->profile->l_age_start=$request->input('l_age_start');
            $this->profile->l_age_stop=$request->input('l_age_stop');
            $this->profile->l_height_start=$request->input('l_height_start');
            $this->profile->l_height_stop=$request->input('l_height_stop');
            $this->profile->l_weight_start=$request->input('l_weight_start');
            $this->profile->l_weight_stop=$request->input('l_weight_stop');
            $this->profile->l_horoscope_id=$request->input('l_horoscope_id');

            $this->profile->save();

            /* Passport multi add photos */
            if($this->passport_photos){
                foreach ($this->passport_photos as $p) {
                    $media = new ProfileMedia();
                    $media->media_key = 'passport';
                    $media->media_value = $p;
                    $this->profile->media()->save($media);
                }
            }

            //@todo Загрузка 5 фотографий вкладка.
        }

        \Session::flash('flash_success', 'Девушка успешно добавлена');

        return redirect('/admin/girls');
    }
    public function changePartner(Request $request){
        $request->input('girl_id');
        $user = User::find($request->input('girl_id'));
        $user->partner_id=$request->input('partner_list');
        $user->save();
        return redirect()->back();
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
        return view('admin.profile.girls.girl');
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

        $user = $this->user->find($id);
        $profile_images = $this->profileImages->where('user_id', $id)->get();
        $selects = [
            'gender'    => $this->profile->getEnum('gender'),
            'eyes'       => $this->profile->getEnum('eyes'),
            'hair'      => $this->profile->getEnum('hair'),
            'education' => $this->profile->getEnum('education'),
            'kids'      => $this->profile->getEnum('kids'),
            'kids_live' => $this->profile->getEnum('kids_live'),
            'want_kids'    => $this->profile->getEnum('want_kids'),
            'family'    => $this->profile->getEnum('family'),
            'religion'  => $this->profile->getEnum('religion'),
            'smoke'     => $this->profile->getEnum('smoke'),
            'drink'     => $this->profile->getEnum('drink'),
            'finance_income' => $this->profile->getEnum('finance_income'),
            'english_level' => $this->profile->getEnum('english_level'),
        ];
        $countries = Country::orderBy('name')->get();

        $states = State::all();

        $statuses = Status::all();

        $why = Why::where('uid', '=', $id)->select(['meta_key', 'meta_value'])->get();

        return view('admin.profile.girls.edit')->with([
            'heading'   => 'Редактировать профиль',
            'user'      => $user,
            'selects'   => $selects,
            'countries' => $countries,
            'states'    => $states,
            'statuses'  => $statuses,
            'why'       => $why,
            'albums'    => (new Album)->getAlbums($id),
            'zodiac_list'=>ZodiacSignService::getAll(),
            'profile_images' => $profile_images,
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
        //@todo Refactor this shit (Single responsibility) lol

        $user = User::find($id);
        $profile = Profile::where('user_id', '=', $id)->first();

        $user->webcam = $request->input('webcam') ? 1 : 0;
        $user->hot = $request->input('hot') ? 1 : 0;

        if ( $request->file('avatar')!=null ) {
            $file = $request->file('avatar');
            $user_avatar = time().'-'.$file->getClientOriginalName();
            $destination = public_path().'/uploads';
            $file->move($destination, $user_avatar);
            $user->avatar = $user_avatar;
        }
/*
        if ($request->file('avatar')) {
            $file = $request->file('avatar');
            $user_avatar = time().'-'.$file->getClientOriginalName();
            $destination = public_path().'/uploads/girls/avatars';
            $file->move($destination, $user_avatar);
            $user->avatar = $user_avatar;
        }

        if(!$request->allFiles()['pass_photo']){
            foreach ($request->allFiles()['pass_photo'] as $file) {
                $pass = time().'-'.$file->getClientOriginalName();
                $destination = public_path().'/uploads/girls/passports';
                $file->move($destination, $pass);
                array_push($this->passport_photos, $pass);
            }
        }
*//*
        if ($request->file('avatar')) {
            $user->avatar =  $this->upload($request->file('avatar'));
        }*/
        //var_dump($request->allFiles()['pass_photo']);
/*
        if(!$request->allFiles()['pass_photo']){
            foreach ($request->allFiles()['pass_photo'] as $file) {
                $pass = $this->upload($file);
                array_push($this->passport_photos, $pass);
            }
        }
*/
        //$this->user->avatar = $user_avatar; eye

        $user->first_name = $request->input('first_name');
        $user->last_name  = $request->input('second_name');

        if($request->input('password') !=null){
            $user->password   = bcrypt($request->input('password'));
        }
        $user->country_id = $request->input('country');
        $user->state_id   = $request->input('state');
        $user->city_id    = $request->input('city');

        $profile->birthday = date('Y-m-d',strtotime($request->input('b_day').'-'.$request->input('b_month').'-'.$request->input('b_year')));
        $user->save();
        /* profile DATA */
        //dd($request->file("profile_photo"));
        if($request->file("profile_photo")[0]!=null){
            foreach ($request->file("profile_photo") as $p_image){
                $profile_image = new profileImages();
                $profile_image->url=$this->upload(($p_image));
                $profile_image->user_id=$id;
                $profile_image->save();
            }
        }
        $profile->gender    = $request->input('gender');
        $profile->height    = $request->input('height');
        $profile->weight    = $request->input('weight');
        $profile->eyes       = $request->input('eyes');
        $profile->hair      = $request->input('hair');
        $profile->education = $request->input('education');
        $profile->kids      = $request->input('kids');
        $profile->want_kids = $request->input('want_kids');
        $profile->family    = $request->input('family');
        $profile->religion  = $request->input('religion');
        $profile->smoke     = $request->input('smoke');
        $profile->drink     = $request->input('drink');
        $profile->occupation= $request->input('occupation');
        $profile->kids_live = $request->input('kids_live');
        $profile->finance_income =$request->input('finance_income');
        $profile->about = $request->input('about');
        $profile->know_lang=$request->input('know_lang');
        $profile->english_level=$request->input('english_level');
        $profile->looking = $request->input('looking');
        $profile->l_age_start=$request->input('l_age_start');
        $profile->l_age_stop=$request->input('l_age_stop');
        $profile->l_height_start=$request->input('l_height_start');
        $profile->l_height_stop=$request->input('l_height_stop');
        $profile->l_weight_start=$request->input('l_weight_start');
        $profile->l_weight_stop=$request->input('l_weight_stop');
        $profile->l_horoscope_id=$request->input('l_horoscope_id');
        /* profile DATA */
        $profile->save();


        \Session::flash('flash_success', trans('flash.success_girl_update'));
        return redirect()->back();
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
        $this->user->find($id)->delete();

        return redirect('/admin/girls');
    }

    public function getByStatus($status)
    {
        $s = Status::where('name', 'like', '%'.$status.'%')->first();

        $girls = []; //without -> role moder -> error -> undefined variable girls on line 335

        if (Auth::user()->hasRole('Owner') || Auth::user()->hasRole('Moder')) {
            $girls = User::where('role_id', '=', '5')
                            ->where('status_id', '=', $s->id)
                            ->get();
        } elseif (Auth::user()->hasRole('Partner')) {
            $girls = User::where('role_id', '=', '5')
                    ->where('partner_id', '=', Auth::user()->id)
                    ->where('status_id', '=', $s->id)
                    ->get();
        }

        return view('admin.profile.girls.status')->with([
            'heading' => 'Девушки по статусу анкеты '.$status,
            'girls'   => $girls,
        ]);
    }

    public function changeStatus(Request $request)
    {
        $girl = User::find($request->input('user_id'));
        $girl->status_id = $request->input('id');

        if (!empty($request->input('why'))) {
            $why = Why::where('uid', '=', $request->input('user_id'))->
                        where('meta_key', 'like', '%status_comment%')->get();

            if (empty($why[0])) {
                $why = new Why();
                $why->uid = $request->input('user_id');
                $why->meta_key = 'status_comment';
                $why->meta_value = $request->input('why');
                $why->save();
            } else {
                Why::where('uid', '=', $request->input('user_id'))
                     ->where('meta_key', 'like', '%status_comment%')
                     ->update(['meta_value' => $request->input('why')]);
            }
        }

        $girl->save();
    }

    /**
     * Check existence of passport in database.
     *
     * firstly for ajax request
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function check(Request $request)
    {
        $passp = $this->passport->where('passno', 'like', str_replace(' ', '', $request->input('passno')))->first();

        if ($passp) {
            return response('<span class="bg-danger">Такой номер пасспорта существует в базе</span>', 200);
        } else {
            return response('<span class="bg-success">Номер паспорта в базе не обнаружен</span>', 200);
        }
    }

    private function age($bithday)
    {
        $age = Carbon::now()->diffInYears(Carbon::createFromFormat('d/m/Y', $bithday));

        return $age;
    }


    public function editAlbum($id, $aid)
    {
        $photos = Images::where('album_id', '=', $aid)->get();
        $album_Data = Album::where('id', '=' ,$aid)->get()[0];
        return view('admin.profile.girls.editAlbume')->with([
            'heading' => 'Edit Albume',
            'photos' => $photos,
            'id'     => $id,
            'album' => $album_Data,
        ]);
    }
    /**
     * Create new album
     *
     * @param int $id
     * @return mixed
     */
    public function createAlbum($id)
    {
        return view('admin.profile.girls.createAlbum')->with([
            'heading' => 'Albums',
            'user_id' => $id,
        ]);
    }

    /**
     * Create and store new album with photos
     *
     * @param Request $request
     * @param $id
     * @return Redirect
     */
    public function addAlbum(Request $request,$id)
    {
        //$id=\Auth::user()->id;
        /**
         * Make new Album
         */
        $album = new Album();
        $album->name          = $request->input('name');
        $album->cover_image   = $this->upload($request->file('cover_image'));
        $album->user_id       = $id;
        $album->save();
        /**
         * Load photos
         */
        foreach ($request->allFiles()['files'] as $file) {
            $image = new Images();
            $image->album_id = $album->id;
            $image->image = $this->upload($file);
            $image->save();
        }
        return redirect('/'.\App::getLocale().'/admin/girl/edit/'.$id);
    }

    public function saveAlbume(Request $request,$id,$aid){
        $album = $this->Album->find($aid);
        $album->name=$request->input('name');
        $files=$request->allFiles();
        if(isset($files['cover_image'])){
            $album->cover_image=$this->upload($files['cover_image']);
        }
        $album->save();

        foreach ($request->allFiles()['files'] as $file) {
            if(!is_null($file)){
                $image = new Images();
                $image->album_id = $aid;
                $image->image = $this->upload($file);
                $image->save();
            }
        }
        return redirect('/'.\App::getLocale().'/admin/girl/edit/'.$id."/edit_album/".$aid);
    }

    public function dropProfileFoto($fid){
        $image = profileImages::find($fid);
        $this->removeFile('/uploads/'.$image->image);
        profileImages::destroy($fid);
        return response('success', 200);
    }
    /**
     * Drop photo
     *
     * @param Request $request
     * @return
     */
    public function dropImageAlbum($aid)
    {
        $image = Images::find($aid);
        $this->removeFile('/uploads/'.$image->image);
        Images::destroy($aid);
        return response('success', 200);
    }

    /**
     * Drop album & files
     *
     * @param Request $request
     * @return mixed
     */
    public function deleteAlbum($albumId)
    {
        $images = Images::where('album_id', '=', $albumId);
        foreach ($images as $i){
            $this->removeFile('/uploads/'.$i->image);
        }

        Album::destroy($albumId);
        return response('success', 200);
    }
}
