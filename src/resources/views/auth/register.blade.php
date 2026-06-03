@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?{{ time() }}">
@endpush

@section('content')
<div class="auth-container">
    <h2>会員登録</h2>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        {{-- ユーザー名 --}}
        <div class="form-group">
            <label for="name">ユーザー名</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}">
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

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

        {{-- パスワード確認 --}}
        <div class="form-group">
            <label for="password_confirmation">確認用パスワード</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
        </div>

        <button type="submit" class="btn-submit">登録する</button>
    </form>

    <a href="{{ route('login') }}" class="link-login">ログインはこちら</a>
</div>
@endsection