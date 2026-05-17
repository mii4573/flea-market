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
                    <select name="payment_method" class="select-input" form="purchase-form">
                        <option value="" disabled selected>選択してください</option>
                        <option value="コンビニ払い">コンビニ払い</option>
                        <option value="クレジットカード">クレジットカード</option>
                    </select>
                </div>
            </div>

            <div class="selection-row">
                <div class="row-header">
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="link-select">変更する</a>
                </div>
                <div class="row-content">
                   @auth
                     {{-- コントローラーで設定した $address (配列) を使用する --}}
                     @if(!empty($address['post_code']) && !empty($address['address']))
                       <p>〒{{ $address['post_code'] }}</p>
                       <p>{{ $address['address'] }}{{ $address['building'] }}</p>
                     @else
                       {{-- プロフィールも一時住所もない場合 --}}
                       <p class="error-text">配送先情報が登録されていません。右上の「変更する」から入力してください。</p>
                     @endif
                    @else
                      <p class="error-text">配送先を表示するにはログインが必要です。</p>
                    @endauth
                </div>
            </div>
        </div>

        <div class="purchase-side">
           <form action="{{ route('purchase.store', ['item_id' => $item->id]) }}" method="POST"  id="purchase-form">  
             @csrf
              <input type="hidden" name="payment_method" id="hidden-payment-method" value=""> 
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
              @error('payment_method')
                <p style="color: red; font-size: 0.8rem;">{{ $message }}</p>
              @enderror
              <button type="submit" class="btn-submit">購入する</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. 左側のセレクトボックスと、右側の表示ラベルを取得
        const paymentSelect = document.querySelector('select[name="payment_method"]');
        const displayLabel = document.getElementById('display-payment-method');

        // 2. セレクトボックスの値が変わった時の処理
        paymentSelect.addEventListener('change', function () {
            
            const selectedText = paymentSelect.options[paymentSelect.selectedIndex].text;
            const selectedValue = paymentSelect.value;
            
            // 右側のラベルを書き換える
            displayLabel.textContent = selectedText;

            document.getElementById('hidden-payment-method').value = selectedValue;
        });
    });
</script>
@endpush
@endsection