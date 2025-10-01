<?php

// use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;




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
Route::get('/item/search', [ItemController::class, 'search'])->name('item.search');

Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserController::class, 'show'])->name('user.mypage');

    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');
});
