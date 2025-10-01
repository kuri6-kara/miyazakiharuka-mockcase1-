<div class="items-list active" id="purchased-items">
    {{-- $items が空でないかチェック --}}
    @forelse ($items as $item)
    <div class="item-card">
        <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
            {{-- 商品画像パスのチェック（Itemモデルに画像パスがある想定） --}}
            @if ($item->item_image_path)
            <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
            @else
            <img src="https://via.placeholder.com/180x180?text=No+Image" alt="画像なし">
            @endif

            <p class="item-name">{{ $item->name }}</p>
            <p class="item-price">¥{{ number_format($item->price) }}</p>
        </a>
    </div>
    @empty
    {{-- 購入履歴がない場合のメッセージ --}}
    <p class="no-items-message">現在、購入履歴はありません。</p>
    @endforelse
</div>