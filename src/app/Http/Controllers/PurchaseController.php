<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index($item_id)
    {
        // 商品IDをもとにDBから商品情報を1件取得
        $item = Item::findOrFail($item_id);

        // 購入確認画面（purchase.index）に商品データを渡して表示
        return view('purchase.index', compact('item'));
    }
}
