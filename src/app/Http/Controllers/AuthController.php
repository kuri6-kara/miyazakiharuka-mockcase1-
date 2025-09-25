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

    // public function register(Request $request): RegisterViewResponse
    // {
    //     return app(RegisterViewResponse::class);
    // }

    // public function store(Request $request)
    // {
    //     $step1Data = $request->only(['name', 'email', 'password']);

    //     $step2Data = $request->only(['weight', 'target_weight']);

    //     $userData = array_merge($step1Data, $step2Data);
    // }
}
