<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $cities;

    public function __construct(City $city)
    {
        $this->cities = $city;
    }

    public function getCityByState(Request $request)
    {
        if(\App::getLocale()=="ru"){
            return $this->cities->where('state_id', '=', $request->input('id'))->orderBy('name', 'ASC')->get();
        }else{
            return $this->cities->where('state_id', '=', $request->input('id'))->orderBy('name_en', 'ASC')->get();
        }

    }
}
