<?php

namespace App\Http\Controllers;

use App\Models\Smiles;
use Illuminate\Http\Request;

use App\Http\Requests;

class SmilesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
    }

    public function sendSmile(Request $request)
    {
        $get_smiles = new Smiles();
        $to = $get_smiles->getSmileFromUser($request->input('id'));

if (!empty($to[0]->to) ){  return 'NO ADD'; }else {

     $smile = new Smiles();
     $smile->from = \Auth::user()->id;
     $smile->to   = $request->input('id');
     $smile->save();
     return response('Success', 200);

}
      /*
     $smile = new Smiles();
     $smile->from = \Auth::user()->id;
     $smile->to   = $request->input('id');
     $smile->save();
     return response('Success', 200);
      */
    }


}
