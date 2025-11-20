@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}" />
@endsection

@section('content')
<div class="profile-edit-container">
    <h2>プロフィール設定</h2>
    <form action="{{ route('register.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="form-group">
            <div class="profile-image-group">
                <div class="profile-image-preview">
                    @if (Auth::user()->profile_image_path)
                    <img src="{{ Storage::url(Auth::user()->profile_image_path) }}" alt="プロフィール画像">
                    @else
                    <img src="{{ Storage::url('icon/人物アイコン.png') }}" alt="デフォルト画像">
                    @endif
                </div>
                <label class="file-label">
                    <input type="file" name="profile_image">
                    画像を選択する
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
        </div>

        <div class="form-group">
            <label for="postcode">郵便番号</label>
            <input type="text" name="postcode" value="{{ old('postcode', '') }}">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', '') }}">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" value="{{ old('building', '') }}">
        </div>
        <button type="submit" class="update-button">プロフィールを登録する</button>
    </form>
</div>
@endsection