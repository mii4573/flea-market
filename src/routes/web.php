<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

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

    // プロフィール・マイページ関連 (ログイン必須)
    Route::middleware('auth')->group(function () {
        Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    // 購入確認画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])
    ->middleware('auth')
    ->name('purchase.store');
    Route::get('/purchase/checkout/{item_id}', [PurchaseController::class, 'checkout'])
    ->middleware('auth')
    ->name('purchase.checkout');
    Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])
    ->middleware('auth')
    ->name('purchase.success');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    Route::middleware('auth')->group(function () {
    // 1. 出品画面を表示するルート
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    
    // 2. 出品ボタンを押した時に、データを保存するルート（POST）
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');
    });

});
