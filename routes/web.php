<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Posts
    Route::get('/', [PostController::class, 'index'])->name('home');
    Route::get('p/create', [PostController::class, 'create'])->name('create_post');
    Route::post('p/create', [PostController::class, 'store'])->name('store_post');
    // slug is the column name we want to search for
    Route::get('p/{post:slug}', [PostController::class, 'show'])->name('show_post');
    Route::get('p/{post:slug}/edit', [PostController::class, 'edit'])->name('edit_post');
    Route::patch('p/{post:slug}/update', [PostController::class, 'update'])->name('update_post');
    Route::delete('p/{post:slug}/delete', [PostController::class, 'destroy'])->name('delete_post');
    // comments
    Route::post('p/{post:slug}/comment', [CommentController::class, 'store'])->name('store_comment');
});
require __DIR__.'/auth.php';
