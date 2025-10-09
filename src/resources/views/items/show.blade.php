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
                    <!-- いいねボタン -->
                    @auth
                    <button
                        id="like-button"
                        data-item-id="{{ $item->id }}"
                        data-is-liked="{{ $is_liked ? 'true' : 'false' }}"
                        class="like-button @if($is_liked) liked @endif">
                        <span id="like-icon">
                            @if($is_liked)
                            <!-- いいね済み（色付き）アイコン -->
                            ★
                            @else
                            <!-- 未いいね（白抜き）アイコン -->
                            ☆
                            @endif
                        </span>
                        <span id="likes-count">{{ $item->likes->count() }}</span>
                    </button>
                    @endauth
                    @guest
                    <span class="likes-count">★ {{ $item->likes->count() }}</span>
                    @endguest

                    <span class="comments-count">💬 {{ $item->comments->count() }}</span>
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
                            {{ $category->category }}
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
                                likeIcon.innerHTML = '★'; // いいね済みアイコン
                                this.classList.add('liked');
                                likesCountSpan.textContent = currentCount + 1;
                            } else {
                                likeIcon.innerHTML = '☆'; // 未いいねアイコン
                                this.classList.remove('liked');
                                likesCountSpan.textContent = currentCount - 1;
                            }
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