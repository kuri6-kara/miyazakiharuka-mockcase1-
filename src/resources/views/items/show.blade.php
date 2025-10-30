@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}" />
@endsection

@section('content')

@php
$is_liked = Auth::check() ? $item->likes->contains('user_id', Auth::id()) : false;
@endphp

<div class="show-container">
    <div class="show-item">
        <div class="show-item__image">
            <img src="{{ Storage::url($item->item_image_path) }}" alt="{{ $item->name }}">
        </div>
        <div class="show-item__details">
            <h1 class="item-name">{{ $item->name }}</h1>
            <p class="brand-name">{{ $item->brand_name }}</p>
            <p class="price">¥{{ number_format($item->price) }}<span>(税込)</span></p>

            <div class="item-actions">
                <div class="likes-comments">
                    <!-- いいねボタン (ログインユーザー) -->
                    @auth
                    <!-- button要素自体が action-column を持つため、構造は維持 -->
                    <button
                        id="like-button"
                        data-item-id="{{ $item->id }}"
                        data-is-liked="{{ $is_liked ? 'true' : 'false' }}"
                        class="like-button @if($is_liked) liked @endif action-column">
                        <span id="like-icon">
                            @if($is_liked)
                            <!-- いいね済み（星型アイコン） -->
                            <img src="{{ Storage::url('icon/star-icon.png') }}" alt="いいね済みアイコン">
                            @else
                            <!-- 未いいね（星型アイコン） -->
                            <img src="{{ Storage::url('icon/star-icon.png') }}" alt="いいねアイコン">
                            @endif
                        </span>
                        <span id="likes-count">{{ $item->likes->count() }}</span>
                    </button>
                    @endauth

                    <!-- いいね表示 (ゲストユーザー) -->
                    @guest
                    <!-- [修正点] 数字を専用のspanで囲み、imgと数字のspanの2つの子要素にする -->
                    <span class="likes-count action-column">
                        <img src="{{ Storage::url('icon/hoshigata-icon.png') }}" alt="いいねアイコン" class="icon-img">
                        <span class="count-number">{{ $item->likes->count() }}</span>
                    </span>
                    @endguest

                    <!-- コメントアイコン -->
                    <!-- [修正点] 数字を専用のspanで囲み、imgと数字のspanの2つの子要素にする -->
                    <span class="comments-count action-column">
                        <img src="{{ Storage::url('icon/hukidasi-icon.png') }}" alt="吹き出しアイコン" class="icon-img">
                        <span class="count-number">{{ $item->comments->count() }}</span>
                    </span>
                </div>

                @if (!$item->is_sold)
                <a href="{{ route('purchase.create', ['item_id' => $item->id]) }}" class="buy-button-link">
                    <button class="buy-button">購入手続きへ</button>
                </a>
                @else
                <button class="buy-button sold-out" disabled>SOLD OUT</button>
                @endif
            </div>

            <div class="item-description">
                <h2>商品説明</h2>
                <p>{{ $item->description }}</p>
            </div>

            <div class="item-info">
                <h2>商品の情報</h2>
                <table>
                    <tr>
                        <th>カテゴリー</th>
                        <td>
                            @foreach ($item->categories as $category)
                            <span class="category-tag">{{ $category->category }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>商品の状態</th>
                        <td>{{ $item->condition }}</td>
                    </tr>
                </table>
            </div>

            <div class="item-comments">
                <h2>コメント({{ $item->comments->count() }})</h2>
                @foreach ($item->comments as $comment)
                <div class="comment">
                    <div class="comment-header">
                        <!-- ユーザー画像を表示する部分 -->
                        <div class="comment-user-image">
                            @if ($comment->user->profile_image_path)
                            <img src="{{ Storage::url($comment->user->profile_image_path) }}" alt="{{ $comment->user->name }}のプロフィール画像" class="profile-icon">
                            @else
                            <div class="profile-placeholder">👤</div>
                            @endif
                        </div>
                        <p class="comment-user">{{ $comment->user->name }}</p>
                    </div>
                    <p class="comment-text">{{ $comment->comment }}</p>
                    <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
                <form action="{{ route('comment.store', ['item_id' => $item->id]) }}" method="POST" novalidate>
                    @csrf
                    <textarea name="comment" placeholder="コメントを追加"></textarea>
                    <button type="submit">コメントを送信</button>

                    <div class="form__error">
                        @error('comment')
                        {{ $message }}
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.getElementById('like-button');
        const likeIcon = document.getElementById('like-icon');
        const likesCountSpan = document.getElementById('likes-count');

        // アイコン画像要素を操作するための関数
        function updateLikeIcon(isLiked) {
            const imgElement = likeIcon.querySelector('img');
            if (imgElement) {
                // いいね済みの場合、赤色や塗りつぶし画像、未いいねの場合、白抜き画像など、
                // 実際の画像ファイル名に合わせてパスを調整してください。
                // 例として、ここではシンプルに alt テキストのみを更新します。
                imgElement.alt = isLiked ? 'いいね済みアイコン' : 'いいねアイコン';
                // 画像の色を変える場合は、CSSフィルターを適用するためにクラスのトグルが必要です
            }
        }

        if (likeButton) {
            likeButton.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                let isLiked = this.dataset.isLiked === 'true';
                let currentCount = parseInt(likesCountSpan.textContent);

                // CSRFトークンを取得
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/items/${itemId}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            item_id: itemId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // 状態を反転
                            isLiked = data.action === 'attached';
                            this.dataset.isLiked = isLiked ? 'true' : 'false';

                            // ビューを更新
                            if (isLiked) {
                                this.classList.add('liked');
                                likesCountSpan.textContent = currentCount + 1;
                            } else {
                                this.classList.remove('liked');
                                likesCountSpan.textContent = currentCount - 1;
                            }
                            // アイコン画像を更新
                            updateLikeIcon(isLiked);
                        } else if (data.status === 'error' && data.message === 'unauthenticated') {
                            // 認証エラーの場合
                            window.location.href = '/login';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }
    });
</script>

@endsection