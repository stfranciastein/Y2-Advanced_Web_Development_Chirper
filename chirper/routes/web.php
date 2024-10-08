<?php

use App\Http\Controllers\ChirpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
    // $route->post('profile/{profileId}/follow', 'ProfileController@followUser')->name('user.follow');
    // $route->post('/{profileId}/unfollow', 'ProfileController@unFollowUser')->name('user.unfollow');
    Route::post('/profile/follow/{user}', [ProfileController::class, 'follow'])->name('profile.follow');
    Route::delete('/profile/unfollow/{user}', [ProfileController::class, 'unfollow'])->name('profile.unfollow');
    //Following Page
    Route::get('/followings', [ProfileController::class, 'followings'])->name('followings.index');
});

Route::resource('chirps', ChirpController::class)
->only(['index', 'store', 'edit', 'update', 'destroy', 'follow', 'unfollow'])
->middleware(['auth', 'verified']);


require __DIR__.'/auth.php';
