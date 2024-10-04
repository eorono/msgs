<?php

use App\Http\Controllers\GetSentMessages;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SendMessage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('sent', GetSentMessages::class)->name('sent');
    Route::post('send', SendMessage::class)->name('send');
});



require __DIR__.'/auth.php';
