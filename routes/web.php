<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

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
Route::resource('items', ItemController::class);
