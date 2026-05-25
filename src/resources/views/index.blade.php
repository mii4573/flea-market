@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="tabs">
        <!-- 💡 おすすめタブ：リンク先を「/」にして、検索キーワードがあれば引き継ぐ -->
        <a href="{{ url('/' . ( !empty($keyword) ? '?keyword=' . urlencode($keyword) : '' )) }}" 
           class="tab-item {{ !Request::is('mylist') ? 'active' : '' }}">おすすめ</a>
        
        @auth
            <!-- 💡 マイリストタブ：リンク先を「/mylist」にして、検索キーワードがあれば引き継ぐ -->
            <a href="{{ url('/mylist' . ( !empty($keyword) ? '?keyword=' . urlencode($keyword) : '' )) }}" 
               class="tab-item {{ Request::is('mylist') ? 'active' : '' }}">マイリスト</a>
        @endauth
    </div>

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