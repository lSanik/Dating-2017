<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Smiles extends Model
{
    protected $table = 'smiles';

    public function fromUser()
    {
        return $this->belongsTo('App\Models\User', 'from');
    }

    public function toUser()
    {
        return $this->belongsTo('App\Models\User', 'to');
    }

    public function getSmileFromUser($to){
        
        $smile = \DB::table('smiles')->where('from','=',\Auth::user()->id)->where('to','=',$to)->select('to')->get();
        return $smile;
    }
}
