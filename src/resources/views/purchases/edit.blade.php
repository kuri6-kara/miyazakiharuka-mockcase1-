@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_edit.css') }}" />
@endsection

@section('content')
<div class="purchase-container">
    <h1 class="purchase-title">送付先住所変更画面</h1>

    {{-- 中央寄せのコンテナ (画像のデザインに合わせる) --}}
    <div class="address-change-card">
        <h2 class="section-heading mb-6 text-center">住所の変更</h2>

        {{-- 修正点1: フォームアクションを既存の POST ルート purchase.update に戻す --}}
        <form method="POST" action="{{ route('purchase.update', ['item_id' => $item->id]) }}">
            @csrf

            {{-- 修正点2: 選択中の payment_method_id を保持するための隠しフィールドを追加 --}}
            {{-- $selected_payment_method_id は PurchaseController の edit メソッドから渡されます --}}
            <input type="hidden" name="payment_method_id" value="{{ $selected_payment_method_id ?? '' }}">

            {{-- 郵便番号 --}}
            <div class="form-group">
                <label for="postcode" class="form-label">郵便番号</label>
                <input type="text" name="postcode" id="postcode"
                    value="{{ old('postcode', $address_data['postcode'] ?? '') }}"
                    class="form-input">
                @error('postcode')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 住所 --}}
            <div class="form-group">
                <label for="address" class="form-label">住所</label>
                <input type="text" name="address" id="address"
                    value="{{ old('address', $address_data['address'] ?? '') }}"
                    class="form-input">
                @error('address')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 建物名 --}}
            <div class="form-group mb-6">
                <label for="building" class="form-label">建物名</label>
                <input type="text" name="building" id="building"
                    value="{{ old('building', $address_data['building'] ?? '') }}"
                    class="form-input">
                @error('building')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- 更新ボタン --}}
            <button type="submit" class="submit-button">更新する</button>
        </form>
    </div>
</div>
@endsection