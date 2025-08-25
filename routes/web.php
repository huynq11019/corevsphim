<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

// Route::get('dang-nhap', [AuthController::class, 'getLogin'])->name('login');
// Route::post('dang-nhap', [AuthController::class, 'postLogin'])->name('login');
// Route::get('dang-ky', [AuthController::class, 'getRegister'])->name('register');
// Route::post('dang-ky', [AuthController::class, 'postRegister'])->name('register');
// Route::get('dang-xuat', [AuthController::class, 'getLogout'])->name('logout');
// Route::get('user/profile', [AuthController::class, 'profile'])->name('profile');
// Route::post('user/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
// Route::get('user/change-password', [AuthController::class, 'changePassword'])->name('changePassword');
// Route::post('user/change-password', [AuthController::class, 'updatePassword'])->name('changePassword.update');
// Route::get('user/bookmark', [AuthController::class, 'bookmark'])->name('bookmark');
// Route::get('user/history', [AuthController::class, 'history'])->name('history');
Route::post('theo-doi', [\App\Http\Controllers\AuthController::class, 'follow'])->name('thempho.follow');
Route::post('binh-luan-custom', [\App\Http\Controllers\AuthController::class, 'comment'])->name('comment.store.custom');
Route::post('load-more-comment', [\App\Http\Controllers\AuthController::class, 'loadMoreComment'])->name('loadMoreComment');
// Route::get('get-google-sign-in-url', [AuthController::class, 'getGoogleSignInUrl'])->name('loginGoogle');
// Route::get('auth/google/callback', [AuthController::class, 'loginCallback']);

// Debug route
Route::get('/shorts-test', function() {
    return 'Shorts test working!';
});

// Shorts routes
Route::prefix('shorts')->name('shorts.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ShortsController::class, 'index'])->name('index');
    Route::get('/feed', [\App\Http\Controllers\ShortsController::class, 'feed'])->name('feed');
    Route::get('/trending', [\App\Http\Controllers\ShortsController::class, 'trending'])->name('trending');
    Route::get('/search', [\App\Http\Controllers\ShortsController::class, 'search'])->name('search');
    Route::get('/hashtag/{hashtag}', [\App\Http\Controllers\ShortsController::class, 'hashtag'])->name('hashtag');
    Route::get('/{slug}', [\App\Http\Controllers\ShortsController::class, 'show'])->name('show');

    // AJAX routes for interactions
    Route::post('/{episode}/like', [\App\Http\Controllers\ShortsController::class, 'like'])->name('like');
    Route::post('/{episode}/dislike', [\App\Http\Controllers\ShortsController::class, 'dislike'])->name('dislike');
    Route::post('/{episode}/share', [\App\Http\Controllers\ShortsController::class, 'share'])->name('share');
    Route::post('/{episode}/comment', [\App\Http\Controllers\ShortsController::class, 'comment'])->name('comment');
    Route::post('/{episode}/view', [\App\Http\Controllers\ShortsController::class, 'view'])->name('view');
    Route::get('/{episode}/comments', [\App\Http\Controllers\ShortsController::class, 'comments'])->name('comments');
});

// Admin routes for Backpack
Route::group([
    'prefix' => 'admin',
    'middleware' => ['web'],
], function () {
    Route::resource('episode-shorts', 'App\Http\Controllers\Admin\EpisodeShortsController');
});
