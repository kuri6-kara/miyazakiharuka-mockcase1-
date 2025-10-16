@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}" />
@endsection

@section('content')
<div class="user-profile">
    {{-- プロフィールアイコンと情報 (中央寄せを維持) --}}
    <div class="profile-header-wrapper">
        <div class="profile-header">
            <div class="user-icon">
                @if (Auth::user()->profile_image_path)
                <img src="{{ Storage::url(Auth::user()->profile_image_path) }}" alt="プロフィール画像">
                @else
                <img src="{{ asset('image/人物アイコン.png') }}" alt="デフォルト画像">
                @endif
            </div>
            {{-- ★ 修正箇所: ユーザー名と編集ボタンを新しいラッパーで囲む ★ --}}
            <div class="user-info">
                {{-- ユーザー名と編集ボタンを横並びにするためのラッパーを追加 --}}
                <div class="user-name-and-edit">
                    <h2>{{ $user->name }}</h2>
                    <a href="{{ route('profile.edit') }}" class="profile-edit-button">プロフィールを編集</a>
                </div>
            </div>
            {{-- ★ 修正箇所: ここまで ★ --}}
        </div>
    </div>

    {{-- タブエリアと商品リスト (左端寄せ、画面幅いっぱい) --}}
    <div class="content-full-width-section">
        {{-- タブエリア --}}
        <div class="profile-tabs">
            <a href="{{ route('user.mypage', ['page' => 'sell']) }}" class="tab-item {{ $tab === 'sell' || $tab === null ? 'active' : '' }}">
                出品した商品
            </a>
            <a href="{{ route('user.mypage', ['page' => 'buy']) }}" class="tab-item {{ $tab === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>

        {{-- 商品リストエリア --}}
        <div class="items-section">
            @php
            $activeTab = $tab === 'buy' ? 'buy' : 'sell';

            // 依存関係を減らすため、Bladeからの静的呼び出しではなく、Controllerの処理を利用
            // ここでは便宜上、以前のロジックを一時的に維持します。
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