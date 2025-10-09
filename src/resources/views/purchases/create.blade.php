@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endsection

@section('content')
<div class="purchase-container">
    <h1 class="purchase-title">商品購入画面</h1>

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
                        @foreach ($payment_methods as $method)
                        <option value="{{ $method->payment_method }}">{{ $method->payment_method }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="shipping-info">
                <h2 class="section-heading">配送先</h2>
                <div class="shipping-actions">
                    <a href="{{ route('purchase.edit', ['item_id' => $item->id]) }}" class="change-link">変更する</a>
                </div>

                <div class="address-info">
                    <p class="mb-1">〒 {{ $address_data['postcode'] ?? '---' }}</p>

                    <p>
                        {{ $address_data['address'] ?? '住所が登録されていません' }}

                        @if (!empty($address_data['building']))
                        <span class="ml-1">({{ $address_data['building'] }})</span>
                        @endif
                    </p>
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
                    <span id="selected-payment-name">{{ $payment_methods->first()->payment_method ?? '---' }}</span>
                </div>
            </div>

            <button class="buy-button" type="submit">購入する</button>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('payment_method_select');

        const displaySpan = document.getElementById('selected-payment-name');


        if (select && displaySpan) {
            displaySpan.textContent = select.options[select.selectedIndex].text;
        }

        if (select) {
            select.addEventListener('change', function() {
                const selectedText = this.options[this.selectedIndex].text;

                if (displaySpan) {
                    displaySpan.textContent = selectedText;
                }
            });
        }
    });
</script>

@endsection