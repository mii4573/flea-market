<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'COACHTECH')</title>

    <!-- CSS (Google Fontsや自作CSS) -->
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @stack('css') 
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <a href="/">
                    <img src="{{ asset('img/coachtech_header_logo.png') }}" alt="COACHTECH">
                </a>
            </div>

            <!-- 💡 【FN016】現在開いているURL（/ または /mylist）に対して検索キーワードを送信する -->
            <div class="header__search">
                <form action="{{ Request::is('mylist') ? url('/mylist') : url('/') }}" method="GET">
                    <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ $keyword ?? '' }}">
                </form>
            </div>


            <nav class="header__nav">
                <ul class="nav-list">
                    @auth
                        {{-- ログイン中のみ表示 --}}
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" style="background:none; border:none; cursor:pointer;">ログアウト</button>
                            </form>
                        </li>
                        <li class="nav-item"><a href="/mypage">マイページ</a></li>
                    @else
                        {{-- ログアウト中のみ表示 --}}
                        <li class="nav-item"><a href="/login">ログイン</a></li>
                        <!-- 💡 表記は「マイページ」のまま、遷移先だけ会員登録（/register）に指定 -->
                        <li class="nav-item"><a href="/register">マイページ</a></li>
                    @endauth
                    <li class="nav-item nav-item--btn"><a href="/sell">出品</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>