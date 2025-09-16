<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $inputs = $request->validated();

        $user->update([
            'profile_image_path' => $inputs['profile_image_path'] ?? $user->profile_image_path,
            'name' => $inputs['name'],
            'postcode' => $inputs['postcode'],
            'address' => $inputs['address'],
            'building' => $inputs['building'] ?? null,
            'profile_updated' => true,
        ]);

        return redirect()->route('home');
    }
}
