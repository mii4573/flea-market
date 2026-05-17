@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/address_edit.css') }}">
@endpush

@section('content')
<div class="container">
    <h2>住所の変更</h2>
    <form action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}" method="POST">
        @csrf
        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="post_code" value="{{ old('post_code', $profile->post_code) }}">
            @error('post_code')
              <p style="color: red; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $profile->address) }}">
            @error('address')
             <p style="color: red; font-size: 14px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building" value="{{ old('building', $profile->building) }}">
        </div>
        <button type="submit" class="btn-update">更新する</button>
    </form>
</div>
@endsection