@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_edit.css') }}" />
@endsection

@section('content')
<div class="purchase-container">
    <h1 class="purchase-title">送付先住所変更画面</h1>

    <div class="address-change-card">
        <h2 class="section-heading mb-6 text-center">住所の変更</h2>

        <form method="POST" action="{{ route('purchase.update', ['item_id' => $item->id]) }}">
            @csrf

            <input type="hidden" name="payment_method_id" value="{{ $selected_payment_method_id ?? '' }}">

            <div class="form-group">
                <label for="postcode" class="form-label">郵便番号</label>
                <input type="text" name="postcode" id="postcode"
                    value="{{ old('postcode', $address_data['postcode'] ?? '') }}"
                    class="form-input">
                @error('postcode')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">住所</label>
                <input type="text" name="address" id="address"
                    value="{{ old('address', $address_data['address'] ?? '') }}"
                    class="form-input">
                @error('address')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mb-6">
                <label for="building" class="form-label">建物名</label>
                <input type="text" name="building" id="building"
                    value="{{ old('building', $address_data['building'] ?? '') }}"
                    class="form-input">
                @error('building')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="submit-button">更新する</button>
        </form>
    </div>
</div>
@endsection