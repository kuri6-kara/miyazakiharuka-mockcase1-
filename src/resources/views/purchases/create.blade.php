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
                    <div style="text-align: center; line-height: 100px; font-size: 12px;">商品画像</div>
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
                    <select name="payment_method" class="border p-2 rounded-md">
                        <option value="convenience">コンビニ払い</option>
                        <option value="card">クレジットカード</option>
                        <option value="bank">銀行振込</option>
                    </select>
                </div>
            </div>

            <div class="shipping-info">
                <h2 class="section-heading">配送先</h2>
                <div style="text-align: right; margin-bottom: 10px;">
                    <a href="{{ route('purchase.edit', ['item_id' => $item->id]) }}" class="change-link">変更する</a>
                </div>

                <div class="address-info">
                    <p class="mb-1">〒 {{ $user->postcode ?? '---' }}</p>

                    <p>
                        {{ $user->address ?? '住所が登録されていません' }}

                        @if (!empty($user->building))
                        <span class="ml-1">({{ $user->building }})</span>
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

                <div class="summary-row" style="border-bottom: none;">
                    <span>支払い方法</span>
                    <span>コンビニ払い</span> {{-- 選択されたものを表示する想定 --}}
                </div>
            </div>

            <button class="buy-button" type="submit">購入する</button>

        </div>
    </div>
</div>
@endsection