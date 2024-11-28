<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function create()
    {
        return view('auth.guest-create'); 
    }
}