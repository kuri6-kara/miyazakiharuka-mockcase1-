<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('image/logo.svg') }}" alt="COACHTECH">
                </a>

                <form action="{{ url('/') }}" method="GET" class="search-form">
                    <input type="hidden" name="tab" value="{{ Request::get('tab', 'recommend') }}">
                    <input type="text" name="keyword" placeholder="何をお探しですか？" value="{{ Request::get('keyword') }}" class="search-input">
                    <button type="submit" class="search-button">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </button>
                </form>

                <nav>
                    <ul class="header-nav">
                        <li class="header-nav__item">
                            @if (Auth::check())
                            <a class="header-nav__link" href="/mypage">マイページ</a>
                            @else
                            <a class="header-nav__link" href="/login">マイページ</a>
                            @endif
                        </li>
                        <li class="header-nav__item">
                            @if (Auth::check())
                            <form class="form" action="/logout" method="post" novalidate>
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                            @else
                            <a class="header-nav__link" href="/login">ログイン</a>
                            @endif
                        </li>
                        <li class="header-nav__item">
                            @if (Auth::check())
                            <a href="{{ route('item.create') }}" class="header-button sell-button">出品</a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- ★★★ 修正箇所: この行を追加することで、子ビューの @section('script') の内容がここに読み込まれます ★★★ --}}
    @yield('script')

</body>

</html>