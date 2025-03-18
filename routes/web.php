<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{user}', [ChatController::class, 'index'])->name('chat.private');
    Route::get('/chat/api/conn', [ChatController::class, 'conn'])->name('chat.conn');
    Route::get('/chat/view/{receiver_id}', function ($receiver_id){
        return view('chat.dashboard', compact('receiver_id'));
    });
    // Route::post('/chat/{user}', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{user}', [ChatController::class, 'send'])->name('chat.send');

    Route::get('/search-users', [ChatController::class, 'searchUsers'])->name('search.users');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
