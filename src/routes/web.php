<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web'])->group(function () {

    // ----------------------------------------------------
    // ゲスト（未ログイン）でもアクセス可能なルート
    // ----------------------------------------------------
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


    // ----------------------------------------------------
    // ログイン必須（authミドルウェア）のルート
    // ----------------------------------------------------
    Route::middleware(['auth'])->group(function () {
        
        // マイページ・ユーザープロフィール関連
        Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

        // マイリスト表示
        //Route::get('/mylist', [ItemController::class, 'mylist'])->name('item.mylist');

        // 商品コメント投稿
        Route::post('/item/{item_id}/comment', [ItemController::class, 'storeComment'])->name('comment.store');

        // 商品出品機能
        Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
        Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

        // 商品購入・Stripe決済関連
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');
        Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
        Route::get('/purchase/checkout/{item_id}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
        Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');
        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

        // いいね！機能
        Route::post('/item/{item_id}/like', [LikeController::class, 'store'])->name('like.store');
        Route::delete('/item/{item_id}/like', [LikeController::class, 'destroy'])->name('like.destroy');
    });

});