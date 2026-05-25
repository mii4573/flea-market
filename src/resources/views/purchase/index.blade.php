@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="purchase-container">
        
        {{-- 左側：メインエリア --}}
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
                    {{-- 💡 name属性を活かし、そのままフォームの主役にします。old() も追加しました --}}
                    <select name="payment_method" id="payment-method-select" class="select-input" form="purchase-form" {{ $item->purchase ? 'disabled' : '' }}>
                        <option value="" disabled {{ old('payment_method') ? '' : 'selected' }}>選択してください</option>
                        <option value="コンビニ払い" {{ old('payment_method') === 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                        <option value="クレジットカード" {{ old('payment_method') === 'クレジットカード' ? 'selected' : '' }}>クレジットカード</option>
                    </select>
                </div>
            </div>

            <div class="selection-row">
                <div class="row-header">
                    <h3>配送先</h3>
                    {{-- 売り切れ時は変更できないようにガード --}}
                    @if(!$item->purchase)
                        <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="link-select">変更する</a>
                    @endif
                </div>
                <div class="row-content">
                   @auth
                     @if(!empty($address['post_code']) && !empty($address['address']))
                       <p>〒{{ $address['post_code'] }}</p>
                       <p>{{ $address['address'] }}{{ $address['building'] }}</p>
                     @else
                       <p class="error-text">配送先情報が登録されていません。右上の「変更する」から入力してください。</p>
                     @endif
                    @else
                      <p class="error-text">配送先を表示するにはログインが必要です。</p>
                    @endauth
                </div>
            </div>
        </div>

        {{-- 右側：サイドバー（決済フォーム） --}}
        <div class="purchase-side">
           <form action="{{ route('purchase.store', ['item_id' => $item->id]) }}" method="POST" id="purchase-form">  
             @csrf
             
             {{-- ❌ 競合の元になっていた <input type="hidden" name="payment_method"> は完全に削除しました --}}

             <div class="purchase-side-box">
                  <table class="summary-table">
                      <tr class="table-row">
                        <th>商品代金</th>
                        <td>¥{{ number_format($item->price) }}</td>
                      </tr>
                      <tr class="table-row">
                        <th>支払い方法</th>
                        {{-- 💡 初期状態や画面戻り時にも old() から正しいテキストを出せるように工夫 --}}
                        <td class="selected-method" id="display-payment-method">
                            {{ old('payment_method') ?? '選択してください' }}
                        </td>
                      </tr>
                  </table>
             </div>
             
             @error('payment_method')
                <p style="color: red; font-size: 0.8rem; margin: 8px 0;">{{ $message }}</p>
             @enderror
             
             {{-- 💡 すでに売り切れている場合はボタンを非活性化（disabled）にして購入を防ぐ --}}
             @if($item->purchase)
                 <button type="button" class="btn-submit" style="background-color: #ccc; cursor: not-allowed;" disabled>売り切れました</button>
             @else
                 <button type="submit" class="btn-submit">購入する</button>
             @endif
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 💡 競合を防ぐため、ID指定で明確にセレクトボックスを狙い撃ちします
        const paymentSelect = document.getElementById('payment-method-select');
        const displayLabel = document.getElementById('display-payment-method');

        if (paymentSelect) {
            paymentSelect.addEventListener('change', function () {
                const selectedText = paymentSelect.options[paymentSelect.selectedIndex].text;
                // 右側のテーブル内のラベルテキストのみを安全に書き換える
                displayLabel.textContent = selectedText;
            });
        }
    });
</script>
@endpush
@endsection