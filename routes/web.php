<?php

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

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

Route::get('/', HomeController::class)->name('home');

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

Route::get('articles/download/{article}', [ArticleController::class, 'download'])->name('articles.download');

Route::resource('comments', CommentController::class);

// 개별 프로필 조회 라우트
Route::get('profile/{user:username}', [ProfileController::class, 'show'])
->name('profile')
->where('user', '^[A-Za-z0-9-]+$'); // 정규표현식 제약

Route::post('follow/{user}', [FollowController::class, 'store'])->name('follow');
Route::delete('follow/{user}', [FollowController::class, 'destroy'])->name('unfollow');

Route::post('/users/email', [RegisteredUserController::class, 'email'])->name('users.email');

Route::post('/users/verify', [RegisteredUserController::class, 'verify'])->name('users.verify');

Route::get('/users/email_check', [RegisteredUserController::class, 'emailCheck'])->name('users.email_check');

Route::get('/videos/upload/view', [VideoController::class, 'uploadFileView'])->name('videos.upload_view');

Route::post('/videos/upload', [VideoController::class, 'uploadFile'])->name('videos.uploadFile');

Route::get('/videos/index', [VideoController::class, 'index'])->name('videos.index');

Route::get('/videos/show/{video}', [VideoController::class, 'show'])->name('videos.show');