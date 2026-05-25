<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; /* 💡 古い画像を消すために必要です */
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Profile;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    /**
     * マイページの表示
     */
    public function index()
    {
        $user = Auth::user();

        $profile = $user->profile;
        
        // 出品した商品を取得
        $sellItems = Item::where('seller_id', $user->id)->get();

        // 購入した商品を商品情報（with）と一緒に取得
        $buyItems = Purchase::where('user_id', $user->id)->with('item')->get();

        return view('mypage', compact('user', 'profile', 'sellItems', 'buyItems'));
    }

    /**
     * プロフィール編集画面の表示
     */
    public function edit()
    {
        $user = Auth::user();
        
        // プロフィールがまだ無い場合は新しいインスタンスを作る
        $profile = $user->profile ?? new Profile();

        // 💡 ビューのファイルを「resources/views/profile/edit.blade.php」にする場合は 'profile.edit' にします
        return view('profile_edit', compact('user', 'profile'));
    }

    /**
     * プロフィール情報の更新処理
     */
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        
        // 💡 要件定義(FN027)に合わせ、大元のusersテーブルのnameも一緒に更新
        $user->name = $request->display_name;
        $user->save();
        
        // プロフィールを取得、なければ新規作成
        $profile = $user->profile ?? new Profile();
        $profile->user_id = $user->id;
        $profile->display_name = $request->display_name;
        $profile->post_code = $request->post_code;
        $profile->address = $request->address;
        $profile->building = $request->building;

        // 画像のアップロード処理
        if ($request->hasFile('image')) {
            // 💡 古い画像がすでに登録されていれば、ストレージから削除して綺麗にする
            if ($profile->image_path) {
                Storage::disk('public')->delete($profile->image_path);
            }
            
            // storage/app/public/profiles に保存し、そのパスをデータベースへ
            $path = $request->file('image')->store('profiles', 'public');
            $profile->image_path = $path;
        }

        $profile->save();

        return redirect()->route('mypage')->with('success', 'プロフィールを更新しました！');
    }
}