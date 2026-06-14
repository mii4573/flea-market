<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;                 // 👈 Likeモデルをインポート
use Illuminate\Support\Facades\Auth; // 👈 Auth（ログイン情報）をインポート

class LikeController extends Controller
{
    /**
     *  いいね！を登録する
     */
    public function store($item_id)
    {
        // すでにいいねしていないか確認（二重登録の防止）
        $exists = Like::where('user_id', Auth::id())
                      ->where('item_id', $item_id)
                      ->exists();

        if (!$exists) {
            Like::create([
                'user_id' => Auth::id(),
                'item_id' => $item_id,
            ]);
        }

        // ボタンを押した元の詳細画面にリダイレクトで戻る
        return redirect()->back();
    }

    /**
     *  いいね！を解除する
     */
    public function destroy($item_id)
    {
        // ログイン中のユーザーが、その商品につけたいいねを探す
        $like = Like::where('user_id', Auth::id())
                    ->where('item_id', $item_id)
                    ->first();

        // 見つかったら削除する
        if ($like) {
            $like->delete();
        }

        // ボタンを押した元の詳細画面にリダイレクトで戻る
        return redirect()->back();
    }
}