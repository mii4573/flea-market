@extends('layouts.app')

@push('css')
   <link rel="stylesheet" href="{{ asset('css/exhibit.css') }}?{{ time() }}"> 
@endpush

@section('content')
<div class="exhibit-container">
    <h2>商品の出品</h2>

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 1. 出品商品画像アップロード（真ん中ボタン配置） --}}
        <div class="form-group">
            <label class="form-label">商品画像</label>
            <div class="file-input-wrapper @error('item_image') has-error @enderror">
                <label class="file-custom-btn">
                    <input type="file" name="item_image" accept="image/*" class="form-file-hidden">
                    <span>画像を選択する</span>
                </label>
            </div>
            @error('item_image')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 2. 「商品の詳細」項目（アンダーライン付き） --}}
        <div class="section-title">
            <h3>商品の詳細</h3>
        </div>

        {{-- 3. カテゴリ（複数選択・メルカリ風ボタン反転） --}}
        <div class="form-group">
            <label class="form-label">カテゴリー</label>
            <div class="category-grid">
                @foreach($categories as $category)
                    <label class="category-item">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="category-checkbox"
                            {{ is_array(old('categories')) && in_array($category->id, old('categories')) ? 'checked' : '' }}>
                        <span class="category-badge">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('categories')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 4. 商品の状態 --}}
        <div class="form-group">
            <label class="form-label">商品の状態</label>
            <select name="condition" class="form-control @error('condition') has-error @enderror">
                <option value="">選択してください</option>
                <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
                <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
            </select>
            @error('condition')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 5. 「商品名と説明」項目（アンダーライン付き） --}}
        <div class="section-title">
            <h3>商品名と説明</h3>
        </div>

        {{-- 6. 商品名 --}}
        <div class="form-group">
            <label class="form-label">商品名</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') has-error @enderror" placeholder="商品名を入力してください">
            @error('name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 6. ブランド名 --}}
        <div class="form-group">
            <label class="form-label">ブランド名</label>
            <input type="text" name="brand_name" value="{{ old('brand_name') }}" class="form-control @error('brand_name') has-error @enderror" placeholder="ブランド名を入力してください">
            @error('brand_name')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 6. 商品の説明 --}}
        <div class="form-group">
            <label class="form-label">商品の説明</label>
            <textarea name="description" rows="5" class="form-control textarea-control @error('description') has-error @enderror" placeholder="商品の説明を入力してください">{{ old('description') }}</textarea>
            @error('description')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 6. 販売価格 --}}
        <div class="form-group">
            <label class="form-label">販売価格</label>
            <div class="price-input-container">
                <span class="price-currency">¥</span>
                <input type="number" name="price" value="{{ old('price') }}" class="form-control @error('price') has-error @enderror" placeholder="0">
            </div>
            @error('price')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        {{-- 6. 出品するボタン --}}
        <button type="submit" class="btn-exhibit">出品する</button>
    </form>
</div>
@endsection