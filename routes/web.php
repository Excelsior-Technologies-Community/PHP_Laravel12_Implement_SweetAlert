<?php

use App\Http\Controllers\PostController;

// INDEX
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// CREATE
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');

// STORE
Route::post('/posts/store', [PostController::class, 'store'])->name('posts.store');

// EDIT
Route::get('/posts/edit/{id}', [PostController::class, 'edit'])->name('posts.edit');

// UPDATE
Route::put('/posts/update/{id}', [PostController::class, 'update'])->name('posts.update');

// DELETE
Route::post('/posts/delete/{id}', [PostController::class, 'destroy'])->name('posts.delete');
