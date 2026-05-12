<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
   public function index()
   {
     $user = Auth::user();

     $profile = $user->profile;
    
     $sellItems = Item::where('seller_id', $user->id)->get();

     $buyItems = Purchase::where('user_id', $user->id)->with('item')->get();

     return view('mypage', compact('user', 'profile', 'sellItems', 'buyItems'));

   }

   public function edit()
   {
     $user = Auth::user();
     // プロフィールがまだ無い場合は新しいインスタンスを作る
     $profile = $user->profile ?? new \App\Models\Profile();

     return view('profile_edit', compact('user', 'profile'));
   }

   public function update(ProfileRequest $request)
  {
    $user = Auth::user();
    
    // プロフィールを取得、なければ新規作成
    $profile = $user->profile ?? new \App\Models\Profile();
    $profile->user_id = $user->id;
    $profile->display_name = $request->display_name;
    $profile->post_code = $request->post_code;
    $profile->address = $request->address;
    $profile->building = $request->building;

    // 画像のアップロード処理
    if ($request->hasFile('image')) {
        // storage/app/public/profiles に保存し、そのパスをデータベースへ
        $path = $request->file('image')->store('profiles', 'public');
        $profile->image_path = $path;
    }

    $profile->save();

    return redirect()->route('mypage')->with('success', 'プロフィールを更新しました！');
  }
}
