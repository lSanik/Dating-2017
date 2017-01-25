<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profileImages extends Model
{
    /**
     * @var string
     */
    protected $table = 'profile_images';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'url',
    ];
}
