@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="purchase-container">
        <div class="purchase-main">
            <div class="item-flex">
                <div class="item-image">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    @if($item->purchase)
                      <div class="sold-label">SOLD</div>
                    @endif
                </div>
                <div class="item-detail">
                    <h2>{{ $item->name }}</h2>
                    <p class="price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            <div class="selection-row">
                <div class="row-header">
                    <h3>支払い方法</h3>
                </div>
                <div class="row-content">
                    <select name="payment_method" class="select-input">
                        <option value="" disabled selected>選択してください</option>
                        <option value="konbini">コンビニ払い</option>
                        <option value="card">クレジットカード</option>
                    </select>
                </div>
            </div>

            <div class="selection-row">
                <div class="row-header">
                    <h3>配送先</h3>
                    <a href="#" class="link-select">変更する</a>
                </div>
                <div class="row-content">
                    @auth
                        <p>〒{{ substr(Auth::user()->postal_code, 0, 3) }}-{{ substr(Auth::user()->postal_code, 3) }}</p>
                        <p>{{ Auth::user()->address }}{{ Auth::user()->building }}</p>
                    @else
                        <p class="error-text">配送先を表示するにはログインが必要です。</p>
                    @endauth
                </div>
            </div>
        </div>

        <div class="purchase-side">
            <div class="purchase-side-box">
                <table class="summary-table">
                    <tr class="table-row">
                        <th>商品代金</th>
                        <td>¥{{ number_format($item->price) }}</td>
                    </tr>
                    <tr class="table-row">
                        <th>支払い方法</th>
                        <td class="selected-method" id="display-payment-method">選択してください</td>
                    </tr>
                </table>
            </div>
            <button class="btn-submit">購入する</button>
        </div>
    </div>
</div>
@endsection