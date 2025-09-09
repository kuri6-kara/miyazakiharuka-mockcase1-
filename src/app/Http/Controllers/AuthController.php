<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterViewResponse;

class AuthController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
