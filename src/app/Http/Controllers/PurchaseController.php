<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    public function index($item_id)
    {
        // 商品IDをもとにDBから商品情報を1件取得
        $item = Item::findOrFail($item_id);

        // 1. プロフィールから取得した郵便番号を一度変数に受ける
        $profilePostCode = Auth::user()->profile->post_code ?? '';

        // 2. もし郵便番号が存在し、かつハイフン（-）が含まれていない場合のみ、ここでハイフンを挟む
        if ($profilePostCode && !str_contains($profilePostCode, '-')) {
            $profilePostCode = substr($profilePostCode, 0, 3) . '-' . substr($profilePostCode, 3);
        }

        // 3. セッションがあればそれを使い、なければ補正済みのプロフィール情報を初期値にする
        $address = session('temp_shipping', [
            'post_code' => $profilePostCode, // ハイフン付きに補正した変数を使う
            'address'   => Auth::user()->profile->address ?? '',
            'building'  => Auth::user()->profile->building ?? '',
        ]);

        return view('purchase.index', compact('item', 'address'));
    }

    public function store(Request $request, $item_id)
    {
        $user = Auth::user();
        $profile = $user->profile;

        $temp = session('temp_shipping');
        if ((!$profile || !$profile->post_code || !$profile->address) && !$temp) {
          return back()->with('error', '配送先情報が登録されていません。');
        }  
        
        $request->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => '支払い方法を選択してください',
        ]);

        // ★ 支払い方法によって分岐
        if ($request->payment_method === 'クレジットカード') {
           // カード決済の場合は、一度Stripeのセッション作成へ（データを引き継ぐ）
           // セッション等に支払い方法を一時保存しておくとsuccessで使いやすいです
           session(['payment_method' => 'クレジットカード']);
           return redirect()->route('purchase.checkout', ['item_id' => $item_id]);
        }
   
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
            'payment_method' => $request->payment_method, 
            'shipping_post_code' => $request->session()->get('temp_shipping.post_code') ?? Auth::user()->profile->post_code,
            'shipping_address'   => $request->session()->get('temp_shipping.address')   ?? Auth::user()->profile->address,
            'shipping_building'  => $request->session()->get('temp_shipping.building')  ?? Auth::user()->profile->building,
        ]);

            session()->forget('temp_shipping');
            return redirect()->route('index')->with('message', '購入が完了しました');
    }

    public function checkout($item_id)
    {
        // 1. 商品情報の取得
        $item = Item::findOrFail($item_id);

        // 2. Stripeのシークレットキーをセット
        Stripe::setApiKey(config('services.stripe.secret'));

        // 3. チェックアウトセッションの作成
        $session = Session::create([
            'payment_method_types' => ['card'], // カード決済を指定
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item_id' => $item_id]),
            'cancel_url' => route('purchase.index', ['item_id' => $item_id]), 
        ]);

        return redirect($session->url, 303);
    }

    public function success(Request $request, $item_id)
    {

        // ★ Stripeから戻ってきた時にDBに保存する
        $user = Auth::user();
        $profile = $user->profile;

        // 1. セッション（一時的な配送先）があるか確認。なければプロフィールの住所をデフォルトにする
        $shipping = session('temp_shipping', [
            'post_code' => $profile->post_code ?? '',
            'address'   => $profile->address ?? '',
            'building'  => $profile->building ?? '',
        ]);

        Purchase::create([
          'user_id' => Auth::id(),
          'item_id' => $item_id,
          'payment_method' => session('payment_method', 'クレジットカード'), // セッションから取得
          // ★セッションまたはプロフィールから判定された正しい住所を保存する
          'shipping_post_code' => $shipping['post_code'],
          'shipping_address'   => $shipping['address'],
          'shipping_building'  => $shipping['building'],
        ]);

        session()->forget(['payment_method','temp_shipping']);
    
        return view('purchase.success'); // 成功画面（後で作ります）
    }

    public function editAddress($item_id)
   {
      $item = Item::findOrFail($item_id);
      $profile = session('temp_shipping') ? (object)session('temp_shipping') : Auth::user()->profile;
      return view('purchase.address_edit', compact('item', 'profile'));
   }

    public function updateAddress(AddressRequest $request, $item_id)
    {
       
       session(['temp_shipping' => [
         'post_code' => $request->post_code,
         'address' => $request->address,
         'building' => $request->building,
       ]]);

      return redirect()->route('purchase.index', ['item_id' => $item_id]);
    }
}
