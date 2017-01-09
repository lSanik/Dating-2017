<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StatesController extends Controller
{
    private $states;

    public function __construct(State $states)
    {
        $this->states = $states;
    }

    public function statesByCountry(Request $request)
    {
        //@TODO BUG FIX(SORT) var_dump(\App::getLocale());
        if(\App::getLocale()=="ru"){
            return $this->states->where('country_id', '=', $request->input('id'))->orderBy('name', 'ASC')->get();
        }else{
            return $this->states->where('country_id', '=', $request->input('id'))->orderBy('name_en', 'ASC')->get();
        }

    }
}
