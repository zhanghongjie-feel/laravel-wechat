<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class testController extends Controller
{
    public function a(){
        return view('php');
    }

    public function switch(){
        return view('Study.switch');
    }
    public function while(){
        return view('Study.while');
    }
}
