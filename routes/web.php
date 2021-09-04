<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('product');

// Product routes
Route::post('/products', [App\Http\Controllers\ProductController::class, 'store']);
Route::get('/products/get_all', [App\Http\Controllers\ProductController::class,'get'])->name('get_all');
Route::delete('/products/{id}', [App\Http\Controllers\ProductController::class, 'destroy']);
Route::post('/product/update/{id}', [App\Http\Controllers\ProductController::class, 'update']);

