<?php

// use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Models\Purchase;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Fortify::routes();


Route::get('/', [ItemController::class, 'index'])->name('item.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
// ★★★ 修正箇所: 不要な検索ルートを削除しました ★★★
// Route::get('/item/search', [ItemController::class, 'search'])->name('item.search');

Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserController::class, 'show'])->name('user.mypage');

    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');

    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'update'])->name('purchase.update');

    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');


    Route::post('/items/{item_id}/like', [LikeController::class, 'store'])->name('like.store');

    Route::post('/items/{item_id}/comments', [CommentController::class, 'store'])->name('comment.store');

    // 商品出品画面の表示
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    // 商品出品処理
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');
});
