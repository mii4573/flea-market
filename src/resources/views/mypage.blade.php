@extends('layouts.app')

@push('css')
   <link rel="stylesheet" href="{{ asset('css/mypage.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="container">
    <div class="profile-header">
        <div class="profile-image">
            {{-- 画像があれば表示、なければデフォルト --}}
            <img src="{{ (isset($profile) && $profile->image_path) ? asset('storage/' . $profile->image_path) : asset('images/default-avatar.png') }}" alt="">
        </div>
        
        <h2 class="user-name">{{ $profile->display_name ?? $user->name }}</h2>
        
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-danger">プロフィールを編集</a>
    </div>
    @if (session('success'))
       <div style="color: green; background: #e6ffe6; padding: 10px; margin-bottom: 20px; border: 1px solid green; border-radius: 5px;">
        {{ session('success') }}
    </div>
    @endif

    <div class="tabs">
        <a href="{{ route('mypage', ['tab' => 'sell']) }}" class="tab-item {{ request('tab') != 'buy' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage', ['tab' => 'buy']) }}" class="tab-item {{ request('tab') == 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="item-grid">
        @if(request('tab') == 'buy')
            @forelse($buyItems as $purchase)
                <div class="item-card">
                    <img src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}">
                    <p>{{ $purchase->item->name }}</p>
                </div>
            @empty
                <p>購入した商品はありません</p>
            @endforelse
        @else
            @forelse($sellItems as $item)
                <div class="item-card">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    <p>{{ $item->name }}</p>
                </div>
            @empty
                <p>出品した商品はありません</p>
            @endforelse
        @endif
    </div>
</div>
@endsection