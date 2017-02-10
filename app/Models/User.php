<?php

namespace App\Models;

use Hamedmehryar\Chat\Traits\Chatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use \HighIdeas\UsersOnline\Traits\UsersOnlineTrait;
    use Chatable;
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status_id',
        'partner_id',
        'role_id',
        'city_id',
        'state_id',
        'country_id',
        'avatar',
        'webcam',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesFrom()
    {
        return $this->hasMany('App\Models\Messages', 'from_user', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesTo()
    {
        return $this->hasMany('App\Models\Messages', 'to_user', 'id');
    }

    /**
     * @return mixed
     */
    public function isOnline()
    {
        return Cache::has('user-is-online-'.$this->id);
    }

    /**
     * @param $need_status
     *
     * @return bool
     */
    public function checkUserStatus($need_status)
    {
        return (strtolower($need_status) == strtolower($this->has_status->name)) ? true : false;
    }

    /** Social auth */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function social()
    {
        return $this->hasMany('App\Models\Social', 'user_id');
    }

    /** User roles  */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }

    /**
     * Check user role.
     *
     * @param $roles
     *
     * @return bool
     */
    public function hasRole($roles)
    {
        $this->have_role = $this->getUserRole();
        // Check if the user is a root account
        if ($this->have_role->name == 'Owner') {
            return true;
        }
        if (is_array($roles)) {
            foreach ($roles as $need_role) {
                if ($this->checkIfUserHasRole($need_role)) {
                    return true;
                }
            }
        } else {
            return $this->checkIfUserHasRole($roles);
        }

        return false;
    }

    /**
     * @return mixed
     */
    private function getUserRole()
    {
        return $this->role()->getResults();
    }

    /**
     * @param $need_role
     *
     * @return bool
     */
    private function checkIfUserHasRole($need_role)
    {
        return (strtolower($need_role) == strtolower($this->have_role->name)) ? true : false;
    }

    /** End roles */

    /** City, state, country */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }
    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    /** End city, state, country */

    /** Presents for partner */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function presents()
    {
        return $this->hasMany('App\Models\Presents', 'partner_id', 'id');
    }

    /** Profile */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne('App\Models\Profile', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function passport()
    {
        return $this->hasOne('App\Models\Passport', 'user_id', 'id');
    }

    /** Tickets Relations */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket', 'froms', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id');
    }

    /**
     * Ticket Message check status.
     *
     * @param $status
     *
     * @return bool
     */
    public function hasStatus($status)
    {
        echo $status;
        $this->has_status = $this->getStatus();
        echo $this->has_status->name;

        if (is_array($status)) {
            foreach ($status as $status) {
                if ($this->checkUserStatus($status)) {
                    return true;
                }
            }
        } else {
            return $this->checkUserStatus($status);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status()->getResults();
    }

    /** Chat messages */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMessages()
    {
        return $this->hasMany('App\Models\ChatMessages', 'user_id');
    }

    /** User finance */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finance()
    {
        return $this->hasMany('App\Models\Finance');
    }

    public function userPay()
    {
        return $this->hasMany('App\Models\Expenses', 'user_id');
    }

    public function girlHave()
    {
        return $this->hasMany('App\Models\Expenses', 'girl_id');
    }

    /** Smiles */
    public function toSmile()
    {
        return $this->hasMany('App\Models\Smiles', 'to');
    }

    public function fromSmile()
    {
        return $this->hasMany('App\Models\Smiles', 'from');
    }

    public static function GirlsByPartner($partner_id){
        return \DB::table('users')->select('id')->where('partner_id','=',$partner_id)->get();
    }
    /** Partner ID */
    public static function getPartnerId($girl_id)
    {
        return \DB::table('users')->select('partner_id')->where('id', '=', $girl_id)->first()->partner_id;
    }

    public static function getName($user_id){
        //return \DB::table('users')->select('partner_id')->where('id', '=', $girl_id)->first()->partner_id;
    }

    public static function getAvatar($user_id){
        //return \DB::table('users')->select('partner_id')->where('id', '=', $girl_id)->first()->partner_id;
    }

    public static function getAge($user_id){
        //return \DB::table('users')->select('partner_id')->where('id', '=', $girl_id)->first()->partner_id;
    }

    public static function getUserShtInfo($user_id){
        return \DB::table('users')->select(['users.id','users.first_name','users.last_name','users.nickname','users.avatar','profile.birthday','sessions.id as sessionID'])
            ->where('users.id','=',$user_id)
            ->leftJoin('profile', 'users.id', '=', 'profile.user_id')
            ->leftJoin('sessions', 'users.id', '=', 'sessions.user_id')
            ->get();
        //return \DB::table('users')->select('partner_id')->where('id', '=', $girl_id)->first()->partner_id;
    }


    public static function  getUserIdSession ($session_key){
        $query=\DB::table('sessions')->select('user_id')->where('id',"=",$session_key)->get();//->user_id
            if(is_array($query)){
                if(array_key_exists(0,$query)){
                    $query=$query[0]->user_id;
                }else{
                    $query='not_found';
                }
            }else{
                $query='not_found';
            }
        return $query;
    }


    public static function  getUserSessionId ($id){
        $query=\DB::table('sessions')->select('id')->where('user_id',"=",$id)->get();//->user_id
        if(is_array($query)){
            if(array_key_exists(0,$query)){
                $query=$query[0]->id;
            }else{
                $query='not_found';
            }
        }else{
            $query='not_found';
        }
        return $query;
    }

}
