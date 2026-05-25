@extends('layouts.app')

@push('css')
    {{-- 会員登録と同じCSSを読み込んでデザインを統一 --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?{{ time() }}">
@endpush

@section('content')
<div class="auth-container">
    <h2>ログイン</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- メールアドレス --}}
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}">
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-group">
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password">
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn-submit">ログインする</button>
    </form>

    <a href="{{ route('register') }}" class="link-login">会員登録はこちら</a>
</div>
@endsection