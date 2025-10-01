@extends('layouts.app')

@section('css')
{{-- 購入画面CSSを流用 --}}
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
<div class="purchase-container">
    <h1 class="purchase-title">送付先住所変更画面</h1>

    <div class="max-w-xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h2 class="section-heading mb-6 text-center">住所の変更</h2>

        <form method="POST" action="{{ route('purchase.update', ['item_id' => $item->id]) }}">
            @csrf

            {{-- 郵便番号 --}}
            <div class="mb-4">
                <label for="postcode" class="block text-gray-700 font-bold mb-2">郵便番号</label>
                <input type="text" name="postcode" id="postcode"
                    value="{{ old('postcode', $address_data['postcode'] ?? '') }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('postcode')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 住所 --}}
            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-bold mb-2">住所</label>
                <input type="text" name="address" id="address"
                    value="{{ old('address', $address_data['address'] ?? '') }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('address')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 建物名 --}}
            <div class="mb-6">
                <label for="building" class="block text-gray-700 font-bold mb-2">建物名</label>
                <input type="text" name="building" id="building"
                    value="{{ old('building', $address_data['building'] ?? '') }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('building')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 更新ボタン --}}
            <button type="submit" class="buy-button">更新する</button>
        </form>
    </div>
</div>
@endsection