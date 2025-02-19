<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ImageController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/cooperation', function () {
    return view('cooperation');
})->name('cooperation');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('posts', AdminPostController::class);
    Route::delete('/images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');
});

require __DIR__.'/auth.php';
