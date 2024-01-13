<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/', [\App\Http\Controllers\GuestbookController::class, 'renderMain'])->name('home');

Route::group(['prefix' => 'message'], function () {
    Route::get('/{message_id}', [\App\Http\Controllers\GuestbookController::class, 'renderMessage'])->withoutMiddleware([Auth::class])->name('message.show');
    Route::post('/', [\App\Http\Controllers\MessageController::class, 'createMessage']);
    Route::post('/{message_id}/edit', [\App\Http\Controllers\MessageController::class, 'editMessage']);
    Route::post('/{message_id}/delete', [\App\Http\Controllers\MessageController::class, 'deleteMessage']);

    Route::post('/{message_id}/reply', [\App\Http\Controllers\CommentController::class, 'createReply']);
    Route::post('/{message_id}/{comment_id}/edit', [\App\Http\Controllers\CommentController::class, 'editComment']);
    Route::post('/{message_id}/{comment_id}/star', [\App\Http\Controllers\CommentController::class, 'toggleFavourite']);
    Route::post('/{message_id}/{comment_id}/delete', [\App\Http\Controllers\CommentController::class, 'deleteComment']);
})->middleware(Auth::class);
