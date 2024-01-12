<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/', [\App\Http\Controllers\GuestbookController::class, 'renderMain'])->name('home');

Route::get('/message/{message_id}', [\App\Http\Controllers\GuestbookController::class, 'renderMessage'])->name('message.show');
Route::post('/message', [\App\Http\Controllers\MessageController::class, 'createMessage']);
Route::post('/message/{message_id}/edit', [\App\Http\Controllers\MessageController::class, 'editMessage']);
Route::post('/message/{message_id}/delete', [\App\Http\Controllers\MessageController::class, 'deleteMessage']);

Route::post('/message/{message_id}/reply', [\App\Http\Controllers\CommentController::class, 'createReply']);
Route::post('/message/{message_id}/{comment_id}/edit', [\App\Http\Controllers\CommentController::class, 'editComment']);
Route::post('/message/{message_id}/{comment_id}/star', [\App\Http\Controllers\CommentController::class, 'toggleFavourite']);
Route::post('/message/{message_id}/{comment_id}/delete', [\App\Http\Controllers\CommentController::class, 'deleteComment']);
