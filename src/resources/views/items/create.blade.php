@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}" />
@endsection

@section('content')
<div class="sell-container">
    <h1 class="page-title">商品の出品</h1>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @error('error')
    <div class="form__error alert-danger">{{ $message }}</div>
    @enderror

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        <section class="form-section image-section">
            <h2>商品画像</h2>
            <div class="image-upload-area">
                <input type="file" id="item_image" name="item_image" accept="image/*" class="image-input">
                <label for="item_image" class="image-label">
                    <span id="image-placeholder" style="{{ old('item_image') ? 'display: none;' : '' }}">画像を選択する</span>
                    <img id="image-preview" src="#" alt="商品画像プレビュー" style="display: none; max-width: 100%; max-height: 200px;">
                </label>
            </div>
            @error('item_image')
            <div class="form__error">{{ $message }}</div>
            @enderror
        </section>

        <section class="form-section details-section">
            <h2>商品の詳細</h2>

            <div class="form-group category-group">
                <label>カテゴリ</label>
                <div class="category-tags">
                    @foreach ($categories as $category)
                    <input
                        type="checkbox"
                        id="category-{{ $category['id'] }}"
                        name="categories[]"
                        value="{{ $category['id'] }}"
                        class="category-checkbox"
                        {{-- old('categories')に値が含まれているかチェックし、チェック状態を保持 --}}
                        @if(is_array(old('categories')) && in_array($category['id'], old('categories'))) checked @endif>
                    <label for="category-{{ $category['id'] }}" class="tag">
                        {{ $category['category'] }}
                    </label>
                    @endforeach
                </div>
                @error('categories')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="condition">商品の状態</label>
                <select id="condition" name="condition" class="input-select">
                    <option value="">選択してください</option>
                    @foreach ($conditions as $condition)
                    <option value="{{ $condition }}" {{ old('condition') == $condition ? 'selected' : '' }}>
                        {{ $condition }}
                    </option>
                    @endforeach
                </select>
                @error('condition')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </section>

        <section class="form-section names-description-section">
            <h2>商品名と説明</h2>

            <div class="form-group">
                <label for="name">商品名</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="input-text" placeholder="商品名">
                @error('name')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="brand_name">ブランド名（任意）</label>
                <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name') }}" class="input-text" placeholder="ブランド名">
            </div>

            <div class="form-group">
                <label for="description">商品の説明</label>
                <textarea id="description" name="description" class="input-textarea" placeholder="商品の詳細を入力してください">{{ old('description') }}</textarea>
                @error('description')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </section>

        <section class="form-section price-section">
            <h2>販売価格</h2>
            <div class="form-group price-input-group">
                <span class="currency-symbol">¥</span>
                <input type="number" id="price" name="price" value="{{ old('price') }}" class="input-text price-input" placeholder="0">
            </div>
            @error('price')
            <div class="form__error">{{ $message }}</div>
            @enderror
        </section>

        <button type="submit" class="submit-button">出品する</button>
    </form>
</div>
@endsection

@section('script')
<script src="{{ asset('js/item_create.js') }}"></script>
@endsection