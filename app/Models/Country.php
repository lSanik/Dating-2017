<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'id', 'sortname', 'name',
    ];
    protected function all_order(){
        if(\App::getLocale()=="ru"){
            return $this->orderBy('name', 'ASC')->get();
        }else{
            return $this->orderBy('name_en', 'ASC')->get();
        }
    }
    protected function state()
    {
        return $this->hasMany('App\Model\State');
    }
    public function user()
    {
        return $this->hasMany('App\Models\User');
    }
}
