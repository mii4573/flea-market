<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'COACHTECH')</title>

    <!-- CSS (Google Fontsや自作CSS) -->
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}"> {{-- リセットCSSがあれば --}}
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @stack('css') {{-- ページ個別のCSSを追加したい時用 --}}
</head>
<body>
    <header class="header">
        <div class="header__inner">
            {{-- ロゴ部分 --}}
            <div class="header__logo">
                <a href="/">
                    <img src="{{ asset('img/coachtech_header_logo.png') }}" alt="COACHTECH">
                </a>
            </div>

            {{-- 検索バー (全ページ共通ならここに配置) --}}
            <div class="header__search">
                <form action="/search" method="GET">
                    <input type="text" name="keyword" placeholder="なにをお探しですか？">
                </form>
            </div>

            {{-- ナビゲーション --}}
            <nav class="header__nav">
                <ul class="nav-list">
                    @auth
                        {{-- ログイン中のみ表示 --}}
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit">ログアウト</button>
                            </form>
                        </li>
                        <li class="nav-item"><a href="/mypage">マイページ</a></li>
                    @else
                        {{-- ログアウト中のみ表示 --}}
                        <li class="nav-item"><a href="/login">ログイン</a></li>
                        <li class="nav-item"><a href="/register">マイページ</a></li>
                    @endauth
                    <li class="nav-item nav-item--btn"><a href="/sell">出品</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        {{-- 各ページのコンテンツがここに注入される --}}
        @yield('content')
    </main>


</body>
</html>