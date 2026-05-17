<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('purchase');
        if (Auth::check()){
            $query->where('seller_id', '!=', Auth::id()); 
        }
        $items = $query->get();

        return view('index', compact('items'));
    }

    public function show($item_id)
    {
        $item = Item::with(['likes', 'comments.user', 'seller', 'purchase'])->findOrFail($item_id);

        return view('item_detail', compact('item'));
    }

    public function create()
    {
        // データベースからすべてのカテゴリーを取得
        $categories = Category::all();

        // 出品画面のBladeに $categories を渡して表示
        return view('item_exhibit', compact('categories'));
    }

    public function store(ExhibitionRequest $request) // 👈 自作したExhibitionRequestで不正入力をガード！
    {
        // 1. 画像アップロード処理 (FN029)
        // 'item_image' フォルダの中にランダムな名前で保存し、公開用（public）のパスを取得します
        // 該当画像は laravel の storage ディレクトリに保存されます
        $imagePath = $request->file('item_image')->store('item_image', 'public');

        // 2. 商品情報の保存 (FN028)
        // お使いのItemsテーブルの基本カラム名（seller_id や image_url等）に合わせています
        $item = Item::create([
            'seller_id'   => Auth::id(), // ログインしている出品者のユーザーID
            'name'        => $request->input('name'),
            'brand_name'  => $request->input('brand_name'),
            'description' => $request->input('description'),
            'condition'   => $request->input('condition'),
            'price'       => $request->input('price'),
            'image_path'   => $imagePath, // 保存した画像のパス（例: item_image/xxxx.png）
        ]);

        // 3. 複数選択されたカテゴリの紐付け (FN028-1-2)
        if ($request->has('categories')) {
            $item->categories()->attach($request->input('categories'));
        }
        

        // 4. 保存が完了したら、商品一覧画面（トップページ）へリダイレクト
        return redirect('/')->with('success', '商品を出品しました！');
    }
}
