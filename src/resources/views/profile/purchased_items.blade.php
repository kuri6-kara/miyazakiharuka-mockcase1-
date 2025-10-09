<div class="items-list active" id="purchased-items">
    @forelse ($items as $purchase)
    <div class="item-card">
        @if ($purchase->item)
        <a href="{{ route('item.show', ['item_id' => $purchase->item->id]) }}">
            <div class="item-image">
                @if ($purchase->item->item_image_path)
                <img src="{{ Storage::url($purchase->item->item_image_path) }}" alt="{{ $purchase->item->name }}">
                @else
                <img src="https://via.placeholder.com/180x180?text=No+Image" alt="画像なし">
                @endif
            </div>

            <p class="item-name">{{ $purchase->item->name }}</p>
            <p class="item-price">¥{{ number_format($purchase->item->price) }}</p>
        </a>
        @endif
    </div>
    @empty
    <p class="no-items-message">現在、購入履歴はありません。</p>
    @endforelse
</div>