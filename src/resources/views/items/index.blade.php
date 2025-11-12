@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="main-container">

    @if (isset($no_items_message))
    <div class="no-items-message-container">
        <p class="no-items-message">{{ $no_items_message }}</p>
    </div>
    @endif

    <div class="tab-container">
        <?php
        $recommend_query = request()->except('tab');
        $recommend_query['tab'] = 'recommend';
        ?>
        <a href="{{ route('item.index', $recommend_query) }}"
            class="tab-item {{ $tab == 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>

        <?php
        $mylist_query = request()->except('tab');
        $mylist_query['tab'] = 'mylist';
        ?>
        <a href="{{ route('item.index', $mylist_query) }}"
            class="tab-item {{ $tab == 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="items-grid">
        @forelse ($items as $item)
        <div class="item-card">
            <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <div class="item-image">
                    @if ($item->item_image_path)
                    <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
                    @else
                    <img src="https://via.placeholder.com/180x180?text=No+Image" alt="画像なし">
                    @endif

                    @if ($item->is_sold)
                    <div class="sold-badge">Sold</div>
                    @endif
                </div>
                <p class="item-name">{{ $item->name }}</p>
            </a>
        </div>
        @empty
        @endforelse
    </div>
</div>
@endsection