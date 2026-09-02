<?php

use App\Http\Controllers\PostController;

Route::get('/posts',                    [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create',             [PostController::class, 'create'])->name('posts.create');
Route::post('/posts/store',             [PostController::class, 'store'])->name('posts.store');
Route::get('/posts/edit/{id}',          [PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/update/{id}',        [PostController::class, 'update'])->name('posts.update');
Route::post('/posts/delete/{id}',       [PostController::class, 'destroy'])->name('posts.delete');

Route::get('/posts/trash',              [PostController::class, 'trash'])->name('posts.trash');
Route::post('/posts/restore/{id}',      [PostController::class, 'restore'])->name('posts.restore');
Route::delete('/posts/force-delete/{id}', [PostController::class, 'forceDelete'])->name('posts.forceDelete');
Route::post('/posts/toggle-status/{id}', [PostController::class, 'toggleStatus'])->name('posts.toggleStatus');

Route::get('/posts/export/csv',         [PostController::class, 'exportCsv'])->name('posts.exportCsv');
Route::get('/posts/export/pdf',         [PostController::class, 'exportPdf'])->name('posts.exportPdf');
