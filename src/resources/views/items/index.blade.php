@extends('layouts.app')

@section('content')
<div class="main-container">
    <div class="tabs">
        <a href="{{ route('item.index', ['tab' => 'recommend']) }}" class="tab-item {{ Request::get('tab', 'recommend') == 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="{{ route('item.index', ['tab' => 'mylist']) }}" class="tab-item {{ Request::get('tab') == 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="items-list">
        @if (!empty($no_items_message))
        <p class="no-items-message">{{ $no_items_message }}</p>
        @else
        @foreach ($items as $item)
        <div class="item-card @if($item->is_sold) sold-out @endif">
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <div class="item-image">
                    @if ($item->is_sold)
                    <div class="sold-badge">SOLD</div>
                    @endif

                    @if ($item->item_image_path)
                    <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
                    @else
                    <img src="https://via.placeholder.com/180x180?text=No+Image" alt="画像なし">
                    @endif
                </div>
                <p class="item-name">{{ $item->name }}</p>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </a>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection