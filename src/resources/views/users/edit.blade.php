@extends('layouts.app')

@section('content')
<div class="profile-edit-container">
    <h2>プロフィール設定</h2>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="form-group">
            <div class="profile-image-preview">
                @if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path))
                <img src="{{ Storage::url($user->profile_image_path) }}" alt="プロフィール画像">
                @else
                <img src="https://via.placeholder.com/150" alt="デフォルト画像">
                @endif
            </div>
            <label class="file-label">
                <input type="file" name="profile_image">
                画像を選択する
            </label>
        </div>

        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}">
        </div>

        <div class="form-group">
            <label for="postcode">郵便番号</label>
            <input type="text" name="postcode" value="{{ old('postcode', $user->postcode) }}">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
        </div>

        <button type="submit" class="update-button">更新する</button>
    </form>
</div>
@endsection