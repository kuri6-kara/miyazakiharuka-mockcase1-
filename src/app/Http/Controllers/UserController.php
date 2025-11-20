<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        $tab = $request->get('page', 'sell');

        return view('users.show', compact('user', 'tab'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    /**
     * @param \App\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $inputs = $request->validated();

        if ($request->hasFile('profile_image')) {
            $path = Storage::disk('public')->putFile('profile_images', $request->file('profile_image'));

            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
            }

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

        return redirect()->route('user.mypage')->with('message', 'プロフィールを更新しました。');
    }

    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editFormRegister()
    {
        $user = Auth::user();
        if ($user->profile_updated) {
            return redirect()->route('register.profile.edit');
        }
        return view('register.edit', compact('user'));
    }

    /**
     * @param \App\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFormRegister(ProfileRequest $request)
    {
        $user = Auth::user();
        $inputs = $request->validated();

        if ($request->hasFile('profile_image')) {
            $path = Storage::disk('public')->putFile('profile_images', $request->file('profile_image'));

            if ($user->profile_image_path) {
                if (basename($user->profile_image_path) !== '人物アイコン.png') {
                    Storage::disk('public')->delete($user->profile_image_path);
                }
            }

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

        return redirect('/')->with('message', 'プロフィール情報を登録し、サービスを開始しました。');
    }
}
