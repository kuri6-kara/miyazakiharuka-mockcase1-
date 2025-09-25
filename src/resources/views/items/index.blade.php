@extends('layouts.app')

@section('content')
<div class="main-container">
    <div class="tabs">
        <a href="{{ route('item.index', ['tab' => 'recommend']) }}" class="tab-item {{ Request::get('tab', 'recommend') == 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ Auth::check() ? route('item.index', ['tab' => 'mylist']) : route('login') }}" class="tab-item {{ Request::get('tab') == 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="items-list">
        @foreach ($items as $item)
        <div class="item-card sold-out">
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
                <p>¥{{ number_format($item->price) }}</p>
            </a>
        </div>
        @endforeach

        @foreach ($items as $item)
        <div class="item-card @if($item->is_sold) sold-out @endif">
            <a href="#">
                <img src="..." alt="商品画像">
                <p>{{ $item->name }}</p>
                @if ($item->is_sold)
                <span class="sold-label">SOLD</span>
                @else
                <p>¥{{ number_format($item->price) }}</p>
                @endif
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection