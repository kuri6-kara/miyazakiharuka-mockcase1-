@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="main-container">

    {{-- 検索結果ない場合のメッセージ --}}
    @if (isset($no_items_message))
    <div class="no-items-message-container">
        <p class="no-items-message">{{ $no_items_message }}</p>
    </div>
    @endif

    <div class="tab-container">
        <!-- おすすめタブ -->
        {{-- URL生成を修正: $keywordがある場合のみクエリに追加する --}}
        <?php
        $recommend_url = url('/') . '?tab=recommend';
        if (!empty($keyword)) {
            $recommend_url .= '&keyword=' . urlencode($keyword);
        }
        ?>
        <a href="{{ $recommend_url }}"
            class="tab-item {{ $tab == 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>

        <!-- マイリストタブ -->
        {{-- URL生成を修正: $keywordがある場合のみクエリに追加する --}}
        <?php
        $mylist_url = url('/') . '?tab=mylist';
        if (!empty($keyword)) {
            $mylist_url .= '&keyword=' . urlencode($keyword);
        }
        ?>
        <a href="{{ $mylist_url }}"
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

                    {{-- SOLDバッジの表示 --}}
                    @if ($item->is_sold)
                    <div class="sold-badge">SOLD</div>
                    @endif
                </div>
                <p class="item-name">{{ $item->name }}</p>
                <p class="item-price">¥{{ number_format($item->price) }}</p>
            </a>
        </div>
        @empty
        @endforelse
    </div>
</div>
@endsection