<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'city', 'country_id',
    ];

    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }
}
