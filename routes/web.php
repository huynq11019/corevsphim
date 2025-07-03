<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
Route::post('theo-doi', [AuthController::class, 'follow'])->name('thempho.follow');
Route::post('binh-luan-custom', [AuthController::class, 'comment'])->name('comment.store.custom');
Route::post('load-more-comment', [AuthController::class, 'loadMoreComment'])->name('loadMoreComment');
// Route::get('get-google-sign-in-url', [AuthController::class, 'getGoogleSignInUrl'])->name('loginGoogle');
// Route::get('auth/google/callback', [AuthController::class, 'loginCallback']);
