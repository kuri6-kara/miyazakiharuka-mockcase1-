<div class="items-list active" id="sold-items">
    @forelse ($items as $item)
    <div class="item-card">
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
    @empty
    <p class="no-items-message">現在、出品中の商品はありません。</p>
    @endforelse
</div>