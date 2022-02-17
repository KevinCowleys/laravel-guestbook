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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/', [\App\Http\Controllers\GuestbookController::class, 'render']);
Route::post('/message', [\App\Http\Controllers\MessageController::class, 'createMessage']);
Route::post('/message/{message_id}/reply/', [\App\Http\Controllers\MessageController::class, 'createAdminReply']);
Route::post('/message/{message_id}/edit/', [\App\Http\Controllers\MessageController::class, 'editMessage']);
Route::post('/message/{message_id}/delete', [\App\Http\Controllers\MessageController::class, 'deleteMessage']);
