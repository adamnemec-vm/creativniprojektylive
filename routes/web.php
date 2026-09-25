<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::view('/cooperation', 'cooperation')->name('cooperation');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('posts', AdminPostController::class)->except('show');
    Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('can:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('categories', AdminCategoryController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
