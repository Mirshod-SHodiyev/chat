<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('home');
   
     Route::post('/message', [MessageController::class, 'sendMessage']);
     Route::get('/messages/{userId}', [MessageController::class, 'getMessages']);
});

require __DIR__.'/auth.php';
