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
         <!-- ==================== 👍 いいね！セクション ==================== -->
         <div class="icon-group">
            @auth
                @if($item->likes->contains('user_id', Auth::id()))
                <!-- 👍 いいね済み（解除用ボタン）：赤いハートを表示 -->
                <form action="{{ route('like.destroy', ['item_id' => $item->id]) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer; display: inline-flex; align-items: center;">
                        <img src="{{ asset('img/heart_pink_icon.png') }}" alt="いいね解除">
                    </button>
                  </form>
                @else
                <!-- 🤍 未いいね（登録用ボタン）：通常のハートを表示 -->
                 <form action="{{ route('like.store', ['item_id' => $item->id]) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer; display: inline-flex; align-items: center;">
                        <img src="{{ asset('img/heart_default_icon.png') }}" alt="いいね登録">
                    </button>
                 </form>
                @endif
            @endauth

            @guest
             <!-- ログインしていない一般ユーザー向け -->
             <a href="{{ route('login') }}" style="display: inline-flex; align-items: center;">
                <img src="{{ asset('img/heart_default_icon.png') }}" alt="いいね">
             </a>
            @endguest

            <!-- いいねの件数カウント -->
            <span class="icon-count">{{ isset($item->likes) ? $item->likes->count() : 0 }}</span>
         </div>

         <!-- ==================== 💬 コメントセクション ==================== -->
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
                       @if($item->categories && $item->categories->count() > 0)
                           @foreach($item->categories as $category)
                               <span class="category-tag">{{ $category->name }}</span>
                           @endforeach
                       @else
                           <span class="text-muted">未設定</span>
                       @endif 
                    </td>
                </tr>

                <tr>
                    <th>商品の状態</th>
                    <td>{{ $item->condition ?? '未設定' }}</td>
                </tr>
            </table>
        </div>

        <!-- ==================== 💬 コメントエリア ==================== -->
        <div class="comment-section">
           <h3>コメント ({{ isset($item->comments) ? $item->comments->count() : 0 }})</h3>
           
           <div class="comment-list">
              @if(isset($item->comments) && $item->comments->count() > 0)
                @foreach($item->comments as $comment)
                  <div class="comment-item">
                    <div class="comment-user">
                        <div class="user-icon">
                            @if(isset($comment->user->image_path))
                                <img src="{{ asset('storage/' . $comment->user->image_path) }}" alt="ユーザー">
                            @else
                               <div class="no-avatar"></div> 
                            @endif
                        </div>
                        <span class="user-name">{{ $comment->user->profile->display_name ?? '名無しさん' }}</span>
                    </div>

                    <div class="comment-content comment-text-box">
                        {{ $comment->comment }}
                    </div>
                  </div>
                @endforeach
              @else
                {{-- コメントが1件もない場合のプレビュー表示 --}}
                <div class="comment-item sample-comment">
                    <div class="comment-user">
                        <div class="user-icon sample-icon"></div>
                        <span class="user-name sample-name">admin</span>
                    </div>
                    <div class="comment-content comment-text-box sample-box">
                        こちらにコメントが入ります。
                    </div>
                </div>
              @endif
           </div>

            <!-- コメント入力フォーム -->
            <div class="comment-form">
               <p>商品へのコメント</p>
               
               @auth
                   <form action="{{ route('comment.store', ['item_id' => $item->id]) }}" method="POST">
                       @csrf
                       <textarea name="comment" class="comment-textarea" placeholder="コメントを入力してください">{{ old('comment') }}</textarea>
                       
                       @error('comment')
                           <p class="error-message">{{ $message }}</p>
                       @enderror
                       
                       <button type="submit" class="btn-comment">コメントを送信する</button>
                    </form>
               @endauth

               @guest
                   <div class="guest-notice">
                       <p>コメントを投稿するにはログインが必要です。</p>
                       <a href="{{ route('login') }}" class="btn-comment">ログイン画面へ</a>
                   </div>
               @endguest
            </div>
        </div>                    
    </div>
</div>
@endsection