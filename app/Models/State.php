<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';

    protected $fillable = [
        'name', 'country_id',
    ];

    public function country()
    {
        $this->belongsTo('App\Models\Country');
    }
    public function city()
    {
        $this->hasMany('App\Models\City');
    }
    public function user()
    {
        $this->hasMany('App\Models\User');
    }
}
