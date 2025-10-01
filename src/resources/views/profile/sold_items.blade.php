<div class="items-list active" id="sold-items">
    @forelse ($items as $item)
    <div class="item-card">
        <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
            <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
            <p>{{ $item->name }}</p>
            <p>¥{{ number_format($item->price) }}</p>
        </a>
    </div>
    @empty
    <p class="no-items-message">現在、出品中の商品はありません。</p>
    @endforelse
</div>