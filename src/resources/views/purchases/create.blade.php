@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
@php
// コントローラから渡された $selected_payment_method_id (変更後のID) を取得
$current_payment_id = (int)old('payment_method_id', $selected_payment_method_id);

// $current_payment_id に一致する支払い方法オブジェクトを $payment_methods から検索
$selected_method = $payment_methods->firstWhere('id', $current_payment_id);

// 概要エリアに表示する初期値を設定
$default_payment_name = $selected_method ? $selected_method->payment_method : '---';
@endphp

<form action="{{ route('purchase.store', ['item_id' => $item->id]) }}" method="post">
    @csrf

    <div class="purchase-container">

        <div class="purchase-content">
            <div class="item-info-section">
                <div class="item-detail">
                    <div class="item-image">
                        @if ($item->item_image_path)
                        <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}" />
                        @else
                        <div class="image-placeholder">商品画像</div>
                        @endif
                    </div>
                    <div>
                        <p class="item-name">{{ $item->name }}</p>
                        <p class="item-price">¥{{ number_format($item->price) }}</p>
                    </div>
                </div>

                <div class="payment-methods">
                    <h2 class="section-heading">支払い方法</h2>
                    <div class="mt-2">
                        <select name="payment_method_id" id="payment_method_select" class="border p-2 rounded-md">
                            <option value="" disabled {{ !old('payment_method_id', $selected_payment_method_id) ? 'selected' : '' }}>選択してください</option>
                            @foreach ($payment_methods as $method)
                            <option value="{{ $method->id }}"
                                {{ (int)old('payment_method_id', $selected_payment_method_id) === $method->id ? 'selected' : '' }}>
                                {{ $method->payment_method }}
                            </option>
                            @endforeach
                        </select>

                        @if ($errors->has('payment_method_id'))
                        <p class="mt-1 text-red-500 text-sm">{{ $errors->first('payment_method_id') }}</p>
                        @endif
                    </div>
                </div>

                <div class="shipping-info">
                    <h2 class="section-heading">配送先</h2>

                    @if ($errors->has('shipping_address_set'))
                    <p class="mt-1 text-red-500 text-sm">{{ $errors->first('shipping_address_set') }}</p>
                    @endif

                    <div class="shipping-actions">
                        <a href="#" id="change-address-link"
                            data-base-url="{{ route('purchase.edit', ['item_id' => $item->id]) }}"
                            class="change-link">
                            変更する
                        </a>
                    </div>

                    <div class="address-info">
                        @if (!empty($address_data['address']))
                        <p class="mb-1">〒 {{ $address_data['postcode'] ?? '---' }}</p>
                        <p>
                            {{ $address_data['address'] }}

                            @if (!empty($address_data['building']))
                            <span class="ml-1">({{ $address_data['building'] }})</span>
                            @endif
                        </p>
                        @else
                        <p class="mb-1">〒 {{ $address_data['postcode'] ?? '---' }}</p>
                        <p class="text-red-500">住所が登録されていません</p>
                        @endif
                    </div>
                </div>

            </div>

            <div class="order-summary-section">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>商品代金</span>
                        <span>¥{{ number_format($item->price) }}</span>
                    </div>

                    <div class="summary-row">
                        <span>支払い方法</span>
                        {{-- 修正箇所: 常に最新の支払い方法名を表示するよう変更 --}}
                        <span id="selected-payment-name">{{ $default_payment_name }}</span>
                    </div>
                </div>

                <button class="buy-button" type="submit">購入する</button>

            </div>
        </div>
    </div>
</form>

@endsection

@section('script')
<script src="{{ asset('js/purchase_create.js') }}"></script>
@endsection