<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index($item_id)
    {
        // 商品IDをもとにDBから商品情報を1件取得
        $item = Item::findOrFail($item_id);

        // 購入確認画面（purchase.index）に商品データを渡して表示
        return view('purchase.index', compact('item'));
    }

    public function store(Request $request, $item_id)
    {
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile || !$profile->post_code || !$profile->address) {
            return back()->with('error', '配送先情報が登録されていません。マイページから設定してください。');
        }
        
        $request->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => '支払い方法を選択してください',
        ]);
   
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
            'payment_method' => $request->payment_method, 
            'shipping_post_code' => Auth::user()->profile->post_code,
            'shipping_address'   => Auth::user()->profile->address,
            'shipping_building'  => Auth::user()->profile->building,
        ]);

    // 3. 完了画面、または商品一覧へリダイレクト
        return redirect()->route('index')->with('message', '購入が完了しました');
    }
}
