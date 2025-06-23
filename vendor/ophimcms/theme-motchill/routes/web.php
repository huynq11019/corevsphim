<?php

use Illuminate\Support\Facades\Route;
use Ophim\ThemeMotchill\Controllers\ThemeMotchillController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
    ),
], function () {
    Route::get('/', [ThemeMotchillController::class, 'index']);
    Route::get('auth/dang-xuat', [ThemeMotchillController::class, 'getLogout'])->name('thempho.logout');
    Route::get('user/profile', [ThemeMotchillController::class, 'profile'])->name('thempho.profile');
    Route::get('user/bookmark', [ThemeMotchillController::class, 'bookmark'])->name('thempho.bookmark');
    Route::get('user/history', [ThemeMotchillController::class, 'history'])->name('thempho.history');
    Route::get('auth/google/callback', [ThemeMotchillController::class, 'loginCallback']);
    Route::get('auth/google/get-google-sign-in-url', [ThemeMotchillController::class, 'getGoogleSignInUrl'])->name('loginGoogle');
    
    Route::get(setting('site_routes_category', '/the-loai/{category}'), [ThemeMotchillController::class, 'getMovieOfCategory'])
        ->where(['category' => '.+', 'id' => '[0-9]+'])
        ->name('categories.movies.index');

    Route::get(setting('site_routes_region', '/quoc-gia/{region}'), [ThemeMotchillController::class, 'getMovieOfRegion'])
        ->where(['region' => '.+', 'id' => '[0-9]+'])
        ->name('regions.movies.index');

    Route::get(setting('site_routes_tag', '/tu-khoa/{tag}'), [ThemeMotchillController::class, 'getMovieOfTag'])
        ->where(['tag' => '.+', 'id' => '[0-9]+'])
        ->name('tags.movies.index');

    Route::get(setting('site_routes_types', '/danh-sach/{type}'), [ThemeMotchillController::class, 'getMovieOfType'])
        ->where(['type' => '.+', 'id' => '[0-9]+'])
        ->name('types.movies.index');

    Route::get(setting('site_routes_actors', '/dien-vien/{actor}'), [ThemeMotchillController::class, 'getMovieOfActor'])
        ->where(['actor' => '.+', 'id' => '[0-9]+'])
        ->name('actors.movies.index');

    Route::get(setting('site_routes_directors', '/dao-dien/{director}'), [ThemeMotchillController::class, 'getMovieOfDirector'])
        ->where(['director' => '.+', 'id' => '[0-9]+'])
        ->name('directors.movies.index');

    Route::get(setting('site_routes_episode', '/phim/{movie}/{episode}-{id}'), [ThemeMotchillController::class, 'getEpisode'])
        ->where(['movie' => '.+', 'movie_id' => '[0-9]+', 'episode' => '.+', 'id' => '[0-9]+'])
        ->name('episodes.show');

    Route::post(sprintf('/%s/{movie}/{episode}/report', config('ophim.routes.movie', 'phim')), [ThemeMotchillController::class, 'reportEpisode'])->name('episodes.report');
    Route::post(sprintf('/%s/{movie}/rate', config('ophim.routes.movie', 'phim')), [ThemeMotchillController::class, 'rateMovie'])->name('movie.rating');

    Route::get(setting('site_routes_movie', '/phim/{movie}'), [ThemeMotchillController::class, 'getMovieOverview'])
        ->where(['movie' => '.+', 'id' => '[0-9]+'])
        ->name('movies.show');

    Route::post('auth/login', [ThemeMotchillController::class, 'login'])->name('login');
    Route::post('auth/register', [ThemeMotchillController::class, 'register'])->name('register');
    Route::post('binh-luan', [ThemeMotchillController::class, 'comment'])->name('thempho.comment');
    Route::post('theo-doi', [ThemeMotchillController::class, 'follow'])->name('thempho.follow');
    Route::post('user/profile', [ThemeMotchillController::class, 'updateProfile'])->name('thempho.profile.update');
    Route::post('user/change-password', [ThemeMotchillController::class, 'updatePassword'])->name('thempho.changePassword.update');
});
