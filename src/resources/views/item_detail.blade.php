@extends('layouts.app')

@push('css')
    {{-- 反映を速めるためのタイムスタンプ付き読み込み --}}
    <link rel="stylesheet" href="{{ asset('css/item_detail.css') }}?{{ time() }}">
@endpush

@section('content')
<div class="item-detail-container">
    
    {{-- 左側：商品画像 --}}
    <div class="item-image">
        @isset($item->image_path)
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
        @endisset

        @isset($item->purchase)
            <div class="sold-label">SOLD</div>
        @endisset
    </div>

    {{-- 右側：商品詳細情報 --}}
    <div class="item-info">
        <h1>{{ $item->name ?? '商品名なし' }}</h1>
        <p class="brand-name">{{ $item->brand_name ?? 'ブランド名なし' }}</p>
        <p class="price">¥{{ isset($item->price) ? number_format($item->price) : '0' }}（税込）</p>

        <div class="stats">
            <div class="icon-group">
                <img src="{{ asset('img/heat_default_icon.png') }}" alt="いいね">
                <span class="icon-count">{{ isset($item->likes) ? $item->likes->count() : 0 }}</span>
            </div>
            <div class="icon-group">
                <img src="{{ asset('img/comment.png') }}" alt="コメント">
                <span class="icon-count">{{ isset($item->comments) ? $item->comments->count() : 0 }}</span>
            </div>
        </div>

        <a href="#" class="btn-buy">購入手続きへ</a>

        <div class="description">
            <h3>商品説明</h3>
            <p>{{ $item->description ?? '説明はありません' }}</p>
        </div>

        <div class="details">
            <h3>商品の情報</h3>
            <table>
                <tr>
                    <th>カテゴリー</th>
                    <td>
                        @isset($item->categories)
                            @foreach($item->categories as $category)
                                <span class="category-tag">{{ $category->name }}</span>
                            @endforeach
                        @endisset
                    </td>
                </tr>
                {{-- 商品の状態も追加しておくと親切です --}}
                <tr>
                    <th>商品の状態</th>
                    <td>{{ $item->condition ?? '未設定' }}</td>
                </tr>
            </table>
        </div>

        {{-- 1-10. コメント表示エリア --}}
        <div class="comment-section">
           <h3>コメント ({{ isset($item->comments) ? $item->comments->count() : 0 }})</h3>
           <div class="comment-list">
              @isset($item->comments)
                @foreach($item->comments as $comment)
                  <div class="comment-item">
                    <div class="comment-user">
                        <div class="user-icon">
                            @if(isset($comment->user->image_path))
                                <img src="{{ asset('storage/' . $comment->user->image_path) }}" alt="ユーザー">
                            @endif
                        </div>
                        <span class="user-name">{{ $comment->user->name ?? '名無しさん' }}</span>
                    </div>
                    {{-- コメント本文の表示を追加 --}}
                    <div class="comment-content">
                        {{ $comment->content }}
                    </div>
                  </div>
                @endforeach
              @endisset
            </div>

            {{-- 1-11. コメント投稿フォーム --}}
            <div class="comment-form">
               <p>商品へのコメント</p>
               <form action="#" method="POST">
                   @csrf
                   <textarea name="comment" class="comment-textarea"></textarea>
                   <button type="submit" class="btn-comment">コメントを送信する</button>
                </form>
            </div>
        </div>                 
    </div>
</div>
@endsection