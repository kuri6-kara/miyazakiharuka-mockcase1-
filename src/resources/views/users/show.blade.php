@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="user-profile">
    <div class="profile-header-wrapper">
        <div class="profile-header">
            <div class="user-icon">
                @if (Auth::user()->profile_image_path)
                <img src="{{ Storage::url(Auth::user()->profile_image_path) }}" alt="プロフィール画像">
                @else
                <img src="{{ Storage::url('icon/人物アイコン.png') }}" alt="デフォルト画像">
                @endif
            </div>
            <div class="user-info">
                <div class="user-name-and-edit">
                    <h2>{{ $user->name }}</h2>
                    <a href="{{ route('profile.edit') }}" class="profile-edit-button">プロフィールを編集</a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-full-width-section">
        <div class="profile-tabs">
            <a href="{{ route('user.mypage', ['page' => 'sell']) }}" class="tab-item {{ $tab === 'sell' || $tab === null ? 'active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('user.mypage', ['page' => 'buy']) }}" class="tab-item {{ $tab === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>

        <div class="items-section">
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
    </div>

</div>
@endsection