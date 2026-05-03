@extends('layouts.app')

@section('content')
<div class="container">
    {{-- タブ切り替え部分 --}}
    <div class="tabs">
        <a href="/" class="tab-item {{ !request('tab') || request('tab') == 'recommend' ? 'active' : '' }}">おすすめ</a>
        
        @auth
            {{-- 認証済みユーザーのみマイリストを表示 --}}
            <a href="/?tab=mylist" class="tab-item {{ request('tab') == 'mylist' ? 'active' : '' }}">マイリスト</a>
        @endauth
    </div>

    {{-- 商品グリッド (4枚ずつ横並び) --}}
    <div class="item-grid">
        @foreach($items as $item)
            <div class="item-card">
                <a href="/item/{{ $item->id }}">
                    <div class="item-image">
                        <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        @if($item->purchase)
                           <div class="sold-label">Sold</div>
                        @endif
                    </div>
                    <div class="item-info">
                        <p class="item-name">{{ $item->name }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
