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
        <div class="item-card @if($item->is_sold) sold-out @endif">
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
                <p>{{ $item->name }}</p>
                @if ($item->is_sold)
                <p class="sold-label">SOLD</p>
                @endif
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection