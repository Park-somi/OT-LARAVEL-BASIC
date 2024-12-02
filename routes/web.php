<?php

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Route::controller(ArticleController::class)->group(function(){
//     Route::get('/articles/create', 'create')->name('articles.create');
//     Route::post('/articles', 'store')->name('articles.store');
//     Route::get('articles', 'index')->name('articles.index');    
//     Route::get('articles/{article}', 'show')->name('articles.show');    
//     Route::get('articles/{article}/edit', 'edit')->name('articles.edit');
//     Route::patch('articles/{article}', 'update')->name('articles.update');
//     Route::delete('articles/{article}', 'destroy')->name('articles.delete');
// });

// 리소스 라우트
// Route::resource('articles', ArticleController::class)->only(['index', 'show']);
Route::resource('articles', ArticleController::class);
