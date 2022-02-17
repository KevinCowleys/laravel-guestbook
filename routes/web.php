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

require __DIR__.'/auth.php';

Route::get('/', [\App\Http\Controllers\GuestbookController::class, 'render'])->name('home');
Route::get('/dashboard', [\App\Http\Controllers\GuestbookController::class, 'render'])->name('dashboard');
Route::post('/message', [\App\Http\Controllers\MessageController::class, 'createMessage']);
Route::post('/message/{message_id}/reply/', [\App\Http\Controllers\MessageController::class, 'createAdminReply']);
Route::post('/message/{message_id}/edit/', [\App\Http\Controllers\MessageController::class, 'editMessage']);
Route::post('/message/{message_id}/editAdmin/', [\App\Http\Controllers\MessageController::class, 'editMessageAdmin']);
Route::post('/message/{message_id}/delete', [\App\Http\Controllers\MessageController::class, 'deleteMessage']);
