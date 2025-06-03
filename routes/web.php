<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

// Route dashboard sebagai alias ke home (supaya error hilang)
Route::get('/dashboard', function () {
    return redirect('/');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    // Halaman utama menampilkan postingan
    Route::get('/', [PostController::class, 'index'])->name('posts.index');

    // Simpan postingan baru
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Like dan unlike postingan
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->middleware('auth')->name('posts.like');
    Route::delete('/posts/{post}/unlike', [PostController::class, 'unlike'])->middleware('auth')->name('posts.unlike');


    // Komentar pada postingan
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->middleware(['auth'])->name('comments.store');
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
