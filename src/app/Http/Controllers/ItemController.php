<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Comment;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('purchase');
        if (Auth::check()){
            $query->where('seller_id', '!=', Auth::id()); 
        }

        // 💡 【FN016】トップ画面（おすすめ）でも商品名での部分一致検索を有効にする
        $keyword = $request->input('keyword');
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        $items = $query->get();

        // 💡 状態保持（タブ切り替え時のリンク用）のために $keyword も一緒に渡す
        return view('index', compact('items', 'keyword'));
    }

    public function show($item_id)
    {
        $item = Item::with(['likes', 'comments.user', 'seller', 'purchase','categories'])->findOrFail($item_id);

        return view('item_detail', compact('item'));
    }

    public function create()
    {
        // データベースからすべてのカテゴリーを取得
        $categories = Category::all();

        // 出品画面のBladeに $categories を渡して表示
        return view('item_exhibit', compact('categories'));
    }

    public function store(ExhibitionRequest $request) 
    {
        // 1. 画像アップロード処理 (FN029)
        $imagePath = $request->file('item_image')->store('item_image', 'public');

        // 2. 商品情報の保存 (FN028)
        $item = Item::create([
            'seller_id'   => Auth::id(), 
            'name'        => $request->input('name'),
            'brand_name'  => $request->input('brand_name'),
            'description' => $request->input('description'),
            'condition'   => $request->input('condition'),
            'price'       => $request->input('price'),
            'image_path'  => $imagePath, 
        ]);

        // 3. 複数選択されたカテゴリの紐付け (FN028-1-2)
        if ($request->has('categories')) {
            $item->categories()->attach($request->input('categories'));
        }
        
        // 4. 保存が完了したら、商品一覧画面（トップページ）へリダイレクト
        return redirect('/')->with('success', '商品を出品しました！');
    }

    public function storeComment(CommentRequest $request, $item_id)
    {
        // 1. ログインしていない場合はログイン画面へ（FN020-1：未ログイン時のガード）
        if (!Auth::check()) {
            return redirect()->route('login');
        }
       
        // 2. コメントをDBに保存
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
            'comment' => $request->comment,
        ]);

        // 3. 元の商品詳細画面にメッセージ付きで戻る
        return back()->with('message', 'コメントを投稿しました');
    }

    /**
     * 【FN016対応】マイリスト（いいねした商品）一覧を表示
     */
    public function mylist(Request $request)
    {
        // 1. 未ログインの場合は空の配列かログイン画面へ（要件に合わせて調整）
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. 自分が「いいね」した商品のクエリを作成
        $query = Item::with('purchase')
            ->whereHas('likes', function ($q) {
                $q->where('user_id', Auth::id());
            });

        // 3. 【FN016】マイリスト側でも商品名（name）で部分一致検索（LIKE）をかける
        $keyword = $request->input('keyword');
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        $items = $query->get();

        return view('index', compact('items', 'keyword'));
    }
}