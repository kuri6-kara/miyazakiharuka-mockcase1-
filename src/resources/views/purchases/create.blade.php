@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_create.css') }}" />
@endsection

@section('content')
<div class="purchase-container">
    <h1 class="purchase-title">商品購入画面</h1>

    <div class="purchase-content">
        {{-- 左側: 商品情報、支払い、配送 --}}
        <div class="item-info-section">
            {{-- 商品詳細 (画像上の上部左側) --}}
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

            {{-- 支払い方法の選択 --}}
            <div class="payment-methods">
                <h2 class="section-heading">支払い方法</h2>
                {{-- 支払い方法選択フォーム (ダミー) --}}
                <div class="mt-2">
                    <select name="payment_method" class="border p-2 rounded-md">
                        <option value="convenience">コンビニ払い</option>
                        <option value="card">クレジットカード</option>
                        <option value="bank">銀行振込</option>
                    </select>
                    {{-- 変更リンクは今回はなし --}}
                </div>
            </div>

            {{-- 配送先情報 --}}
            <div class="shipping-info">
                <h2 class="section-heading">配送先</h2>
                <div style="text-align: right; margin-bottom: 10px;">
                    <a href="#" class="change-link">変更する</a>
                </div>

                {{-- ユーザーの情報を表示 (仮のデータ) --}}
                <div class="address-info">
                    <p class="mb-1">〒 XXX-YYYY</p>
                    <p>ここに住所と建物が入ります</p>
                </div>
            </div>

        </div>

        {{-- 右側: 注文内容の要約 --}}
        <div class="order-summary-section">
            <div class="summary-box">
                {{-- 商品代金 --}}
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>

                {{-- 支払い方法 (選択されたもの) --}}
                <div class="summary-row" style="border-bottom: none;">
                    <span>支払い方法</span>
                    <span>コンビニ払い</span> {{-- 選択されたものを表示する想定 --}}
                </div>
            </div>

            {{-- 購入ボタン --}}
            <button class="buy-button" type="submit">購入する</button>

        </div>
    </div>
</div>
@endsection