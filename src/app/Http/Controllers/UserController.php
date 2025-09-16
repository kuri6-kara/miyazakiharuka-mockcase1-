<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'profile_updated' => true,
        ]);

        return redirect()->route('home')->with('success', 'プロフィールが更新されました。');
    }
}
