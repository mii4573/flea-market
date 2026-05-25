@extends('layouts.app')

@section('title', 'メール認証の確認')

@section('content')
<div class="verify-email__container" style="text-align: center; margin-top: 50px;">
    <h2>会員登録ありがとうございます！</h2>
    <p>現在、仮登録の状態です。</p>
    <p>ご登録いただいたメールアドレスに認証メールをお送りしましたので、<br>メール内のリンクをクリックして本登録を完了させてください。</p>

    <!-- 💡 【FN013】認証メール再送機能のフォーム -->
    <div style="margin-top: 30px;">
        @if (session('status') == 'verification-link-sent')
            <p style="color: green; font-weight: bold;">新しい認証メールを再送信しました！</p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" style="padding: 10px 20px; cursor: pointer;">
                認証メールを再送する
            </button>
        </form>
    </div>
</div>
@endsection