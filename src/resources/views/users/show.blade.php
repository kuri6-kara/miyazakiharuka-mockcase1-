@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}" />
@endsection

@section('content')
<div class="user-profile">
    <div class="profile-header">
        <div class="user-icon">
            @php
            $placeholder = 'https://via.placeholder.com/150';
            $imagePath = $user->profile_image_path;
            $imageUrl = ($imagePath && Storage::disk('public')->exists($imagePath))
            ? Storage::url($imagePath)
            : $placeholder;
            @endphp
            <img src="{{ asset('image/人物アイコン.png') }}" alt="デフォルト画像">
        </div>
        <div class="user-info">
            <h2>{{ $user->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="profile-edit-button">プロフィールを編集</a>
        </div>
    </div>

    <div class="profile-tabs">
        {{-- タブのリンクをクエリパラメータ 'page' に変更 --}}
        <a href="{{ route('user.mypage', ['page' => 'sell']) }}" class="tab-item {{ $tab === 'sell' || $tab === null ? 'active' : '' }}">
            出品した商品
        </a>

        <a href="{{ route('user.mypage', ['page' => 'buy']) }}" class="tab-item {{ $tab === 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    @php
    $activeTab = $tab === 'buy' ? 'buy' : 'sell';

    $viewData = \App\Http\Controllers\ProfileController::index($activeTab)->getData();
    $items = $viewData['items'] ?? collect();
    @endphp

    @if ($activeTab === 'sell')
    @include('profile.sold_items', ['items' => $items, 'tab' => $activeTab])
    @else
    @include('profile.purchased_items', ['items' => $items, 'tab' => $activeTab])
    @endif

</div>
@endsection