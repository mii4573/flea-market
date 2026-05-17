@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/item_detail.css') }}?{{ time() }}">
@endpush

@section('content')
<div class="item-detail-container">
    
    <div class="item-image">
        @isset($item->image_path)
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
        @endisset

        @if($item->purchase)
            <div class="sold-label">SOLD</div>
        @endif
    </div>

    <div class="item-info">
        <h1>{{ $item->name ?? '商品名なし' }}</h1>
        <p class="brand-name">{{ $item->brand_name ?? 'ブランド名なし' }}</p>
        <p class="price">¥{{ isset($item->price) ? number_format($item->price) : '0' }}（税込）</p>

        <div class="stats">
            <div class="icon-group">
                <img src="{{ asset('img/heart_default_icon.png') }}" alt="いいね">
                <span class="icon-count">{{ isset($item->likes) ? $item->likes->count() : 0 }}</span>
            </div>
            <div class="icon-group">
                <img src="{{ asset('img/comment.png') }}" alt="コメント">
                <span class="icon-count">{{ isset($item->comments) ? $item->comments->count() : 0 }}</span>
            </div>
        </div>

        <a href="{{ route('purchase.index', ['item_id' => $item->id])}}" class="btn-buy">購入手続きへ</a>

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
                       {{-- 💡 $item->categories（複数形）が存在し、かつ1件以上ある場合のみループを回します --}}
                       @if($item->categories && $item->categories->count() > 0)
                       @foreach($item->categories as $category)
                        <span class="category-tag" style="margin-right: 5px;">{{ $category->name }}</span>
                      @endforeach
                      @else
                        <span style="color: #999;">未設定</span>
                      @endif 
                    </td>
                </tr>

                <tr>
                    <th>商品の状態</th>
                    <td>{{ $item->condition ?? '未設定' }}</td>
                </tr>
            </table>
        </div>

        <div class="comment-section">
           <h3>コメント ({{ isset($item->comments) ? $item->comments->count() : 0 }})</h3>
           <div class="comment-list">
              @isset($item->comments)
                @foreach($item->comments as $comment)
                  <div class="comment-item" style="margin-bottom: 20px;">
                    {{-- ユーザー情報：アイコン(グレーの丸)と名前 --}}
                    <div class="comment-user" style="display: flex; align-items: center; margin-bottom: 8px;">
                        <div class="user-icon" style="width: 40px; height: 40px; background-color: #e0e0e0; border-radius: 50%; overflow: hidden; margin-right: 10px; display: flex; align-items: center; justify-content: center;">
                            @if(isset($comment->user->image_path))
                                <img src="{{ asset('storage/' . $comment->user->image_path) }}" alt="ユーザー" style="width: 100%; height: 100%; object-fit: cover;">
                            @endif
                        </div>
                        <span class="user-name" style="font-weight: bold;">{{ $comment->user->name ?? '名無しさん' }}</span>
                    </div>


                    <div class="comment-content" style="background-color: #f5f5f5; padding: 15px; border-radius: 8px; color: #333;">

                    </div>
                  </div>
                @endforeach
              @endisset
           </div>

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