<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

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


Route::get('/', [ItemController::class, 'getItems'])->name('getItems');
Route::get('add-to-cart/{id}', [ItemController::class, 'addToCart'])->name('addToCart');
Route::get('shopping-cart', [ItemController::class, 'getCart'])->name('getCart');
Route::get('reduce/{id}', [ItemController::class, 'getReduceByOne'])->name('reduceByOne');
Route::get('remove/{id}', [
    ItemController::class, 'getRemoveItem'
])->name('removeItem');
Route::get('checkout', [ItemController::class, 'postCheckout'])->name('checkout');
Route::prefix('user')->group(function () {
    Route::get('register', [UserController::class, 'register'])->name('user.register');
    Route::post('signup', [UserController::class, 'postSignup'])->name('user.signup');
    Route::get('login', [UserController::class, 'login'])->name('user.login');
    Route::post('signin', [UserController::class,'postSignin'])->name('user.signin');
    // Route::get('profile', [UserController::class, 'getProfile'])->name('user.profile');
});
Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard.index')->middleware('auth');
Route::get('logout', [UserController::class, 'logout'])->middleware('auth')->name('logout');

Route::resource('items', ItemController::class);
