@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form__content">
    <div class="login-form__heading">
        <h2>ログイン</h2>
    </div>
    <form class="form" action="/login" method="post" novalidate>
        @csrf

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">メールアドレス</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="email" name="email" value="{{ old('email') }}" />
                </div>
                <div class="form__error">
                    {{-- 1. メールアドレスの必須エラーのみを表示 (メッセージが「メールアドレスを入力してください」の場合) --}}
                    @error('email')
                    @if ($message === 'メールアドレスを入力してください')
                    {{ $message }}
                    @endif
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">パスワード</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" name="password" />
                </div>
                <div class="form__error">
                    {{-- 2. パスワードの必須エラーを表示 --}}
                    @error('password')
                    {{ $message }}
                    @enderror

                    {{-- 3. 認証失敗エラー（@error('email')に紐づくメッセージ）をここで表示 --}}
                    @error('email')
                    {{-- メッセージが「メールアドレスを入力してください」ではない場合 == 認証失敗エラーの場合 --}}
                    @if ($message !== 'メールアドレスを入力してください')
                    {{ $message }}
                    @endif
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">ログイン</button>
        </div>
    </form>
    <div class="register__link">
        <a class="register__button-submit" href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection