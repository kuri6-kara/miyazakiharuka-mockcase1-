@extends('layouts.app')

@section('content')
<div class="items-list">
    @foreach ($items as $item)
    <div class="item-card">
        <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}">
            <p>{{ $item->name }}</p>
            <p>¥{{ number_format($item->price) }}</p>
        </a>
    </div>
    @endforeach
</div>
@endsection