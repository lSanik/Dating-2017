<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Profile extends Model
{
    protected $table = 'profile';

    protected $fillable = [
        'user_id', 'gender', 'birthday',
        'height', 'weight', 'eyes',
        'hair', 'education', 'kids',
        'want_kids', 'family', 'religion',
        'smoke', 'drink', 'occupation',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function media()
    {
        return $this->hasMany('App\Models\ProfileMedia');
    }

    public function getEnum($field)
    {
        $type = DB::select(DB::raw("SHOW COLUMNS FROM {$this->table} WHERE Field = '{$field}'"))[0]->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches);

        $enum = [];

        foreach (explode(',', $matches[1]) as $value) {
            $v = trim($value, "'");
            $enum = array_add($enum, $v, $v);
        }

        return $enum;
    }
}
