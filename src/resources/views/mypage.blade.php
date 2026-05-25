@extends('layouts.app')

@push('css')
   <link rel="stylesheet" href="{{ asset('css/mypage.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container">
    {{-- ユーザー情報ヘッダー --}}
    <div class="profile-header">
        <div class="profile-image">
            {{-- 💡 画像があれば表示、なければCSSで綺麗なグレーの丸を表示 --}}
            @if(isset($profile) && $profile->image_path)
                <img src="{{ asset('storage/' . $profile->image_path) }}" alt="プロフィール画像" class="profile-circle">
            @else
                <div class="profile-circle-default"></div>
            @endif
        </div>
        
        <h2 class="user-name">{{ $profile->display_name ?? $user->name }}</h2>
        
        <a href="{{ route('profile.edit') }}" class="btn-edit-profile">プロフィールを編集</a>
    </div>

    {{-- フラッシュメッセージ --}}
    @if (session('success'))
       <div class="alert-success">
           {{ session('success') }}
       </div>
    @endif

    {{-- タブメニュー --}}
    <div class="tabs">
        <a href="{{ route('mypage', ['page' => 'sell']) }}" class="tab-item {{ request('page') != 'buy' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['page' => 'buy']) }}" class="tab-item {{ request('page') == 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    {{-- 商品一覧グリッド --}}
    <div class="item-grid">
        @if(request('page') == 'buy')
            {{-- ⬇️ 購入した商品の一覧 ⬇️ --}}
            @forelse($buyItems as $purchase)
                <div class="item-card">
                    {{-- 💡 クリックして商品詳細へ飛べるようにリンク化 --}}
                    <a href="{{ route('item.show', ['item_id' => $purchase->item->id]) }}">
                        <div class="item-image">
                            {{-- 購入した商品はすべて売り切れ状態なので確実にSOLDラベルを出す --}}
                            <span class="sold-label">SOLD</span>
                            <img src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}">
                        </div>
                        <div class="item-info">
                            <span class="item-name">{{ $purchase->item->name }}</span>
                        </div>
                    </a>
                </div>
            @empty
                <p class="empty-message">購入した商品はありません</p>
            @endforelse
        @else
            {{-- ⬇️ 出品した商品の一覧 ⬇️ --}}
            @forelse($sellItems as $item)
                <div class="item-card">
                    {{-- 💡 クリックして商品詳細へ飛べるようにリンク化 --}}
                    <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                        <div class="item-image">
                            {{-- 💡 出品した商品がすでに誰かに買われている場合はSOLDを表示 --}}
                            @if($item->purchase)
                                <span class="sold-label">SOLD</span>
                            @endif
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        </div>
                        <div class="item-info">
                            <span class="item-name">{{ $item->name }}</span>
                        </div>
                    </a>
                </div>
            @empty
                <p class="empty-message">出品した商品はありません</p>
            @endforelse
        @endif
    </div>
</div>
@endsection