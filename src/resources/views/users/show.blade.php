@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}" />
@endsection

@section('content')
<div class="user-profile">
    <div class="profile-header">
        <div class="user-icon">
            <img src="#" alt="プロフィール画像">
        </div>
        <div class="user-info">
            <h2>{{ $user->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="profile-edit-button">プロフィールを編集</a>
        </div>
    </div>

    <div class="profile-tabs">
        <div class="tab-item active" data-tab-target="sold-items">出品した商品</div>
        <div class="tab-item" data-tab-target="purchased-items">購入した商品</div>
    </div>

    <div class="items-list active" id="sold-items">
        @foreach ($soldItems as $item)
        <div class="item-card">
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
            </a>
        </div>
        @endforeach
    </div>

    <div class="items-list" id="purchased-items">
        @foreach ($purchasedItems as $item)
        <div class="item-card">
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
                <p>¥{{ number_format($item->price) }}</p>
            </a>
        </div>
        @endforeach
    </div>

</div>
@endsection


