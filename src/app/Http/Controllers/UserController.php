<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $inputs = $request->validated();

        if ($request->hasFile('profile_image')) {
            $path = Storage::disk('public')->putFile('profile_images', $request->file('profile_image'));
            $inputs['profile_image_path'] = $path;
        }

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
