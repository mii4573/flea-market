<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 全てのルートを 'web' ミドルウェアグループで囲む
// これにより、セッションの開始やクッキーの管理が有効になります
Route::middleware(['web'])->group(function () {

    // 商品関連
    Route::get('/', [ItemController::class, 'index'])->name('item.index');
    Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

    // 会員登録
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // ログイン
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    // ログアウト
     Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // 【追加】プロフィール設定画面（仮）
    // とりあえず画面が表示されるか確認するため、ItemControllerなどで仮の画面を返します
    Route::get('/mypage/profile', [ItemController::class, 'index'])->name('profile.edit');

    // 購入確認画面（商品IDをURLに含める）
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');
});
