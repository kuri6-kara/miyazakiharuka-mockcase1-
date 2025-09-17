<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    COACHTECH
                </a>
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
                            <form class="form" action="/logout" method="post">
                                @csrf
                                <button class="header-nav__button">ログアウト</button>
                            </form>
                            @else
                            <a class="header-nav__link" href="/login">ログイン</a>
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
</body>

</html>