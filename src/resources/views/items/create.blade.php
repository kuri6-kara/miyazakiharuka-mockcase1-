@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}" />
@endsection

@section('content')
<div class="sell-container">
    <h1 class="page-title">商品の出品</h1>

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
        @csrf

        {{-- 商品画像 --}}
        <section class="form-section image-section">
            <h2>商品画像</h2>
            <div class="image-upload-area">
                <input type="file" id="item_image" name="item_image" accept="image/*" class="image-input">
                <label for="item_image" class="image-label">
                    <span id="image-placeholder">画像を選択する</span>
                    <img id="image-preview" src="#" alt="商品画像プレビュー" style="display: none; max-width: 100%; max-height: 200px;">
                </label>
            </div>
            @error('item_image')
            <div class="form__error">{{ $message }}</div>
            @enderror
        </section>

        {{-- 商品の詳細 --}}
        <section class="form-section details-section">
            <h2>商品の詳細</h2>

            <div class="form-group category-group">
                <label>カテゴリ</label>
                <div class="category-tags">
                    {{-- マスタデータがないため、画像に基づきダミーのタグを配置 --}}
                    <span class="tag">ファッション</span>
                    <span class="tag">家電</span>
                    <span class="tag tag-active">インテリア</span>
                    <span class="tag">レディース</span>
                    <span class="tag">メンズ</span>
                    <span class="tag">コスメ</span>
                    <span class="tag">本</span>
                    <span class="tag">ゲーム</span>
                    <span class="tag">スポーツ</span>
                    <span class="tag">キッチン</span>
                    <span class="tag">ハンドメイド</span>
                    <span class="tag">アクセサリー</span>
                    <span class="tag">おもちゃ</span>
                    <span class="tag">ベビー・キッズ</span>
                </div>
                {{-- 実際はhidden inputやJavaScriptでカテゴリIDを送信する --}}
            </div>

            <div class="form-group">
                <label for="condition">商品の状態</label>
                {{-- 実際はマスタデータから取得する --}}
                <select id="condition" name="condition" class="input-select">
                    <option value="" selected>選択してください</option>
                    <option value="新品、未使用">新品、未使用</option>
                    <option value="未使用に近い">未使用に近い</option>
                    <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                    <option value="傷や汚れあり">傷や汚れあり</option>
                    <option value="全体的に状態が悪い">全体的に状態が悪い</option>
                </select>
                @error('condition')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </section>

        {{-- 商品名と説明 --}}
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

        {{-- 販売価格 --}}
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

<script>
    // 画像プレビュー機能
    document.getElementById('item_image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-placeholder');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
            placeholder.style.display = 'block';
        }
    });
</script>
@endsection