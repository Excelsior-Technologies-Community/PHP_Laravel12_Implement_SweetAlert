<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Post CRUD
|--------------------------------------------------------------------------
*/

Route::get('/posts', [PostController::class, 'index'])
    ->name('posts.index');

Route::get('/posts/create', [PostController::class, 'create'])
    ->name('posts.create');

Route::post('/posts/store', [PostController::class, 'store'])
    ->name('posts.store');

Route::get('/posts/edit/{id}', [PostController::class, 'edit'])
    ->name('posts.edit');

Route::put('/posts/update/{id}', [PostController::class, 'update'])
    ->name('posts.update');

Route::post('/posts/delete/{id}', [PostController::class, 'destroy'])
    ->name('posts.delete');


/*
|--------------------------------------------------------------------------
| Trash
|--------------------------------------------------------------------------
*/

Route::get('/posts/trash', [PostController::class, 'trash'])
    ->name('posts.trash');

Route::post('/posts/restore/{id}', [PostController::class, 'restore'])
    ->name('posts.restore');

Route::delete('/posts/force-delete/{id}', [PostController::class, 'forceDelete'])
    ->name('posts.forceDelete');


/*
|--------------------------------------------------------------------------
| AJAX Status Toggle
|--------------------------------------------------------------------------
*/

Route::post('/posts/toggle-status/{id}', [PostController::class, 'toggleStatus'])
    ->name('posts.toggleStatus');


/*
|--------------------------------------------------------------------------
| AJAX Bulk Delete
|--------------------------------------------------------------------------
*/

Route::post('/posts/bulk-delete', [PostController::class, 'bulkDelete'])
    ->name('posts.bulkDelete');


/*
|--------------------------------------------------------------------------
| Export
|--------------------------------------------------------------------------
*/

Route::get('/posts/export/csv', [PostController::class, 'exportCsv'])
    ->name('posts.exportCsv');

Route::get('/posts/export/pdf', [PostController::class, 'exportPdf'])
    ->name('posts.exportPdf');