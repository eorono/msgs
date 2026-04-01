<?php

/**
 * Rutas web de la aplicación.
 *
 * Define las rutas principales de la aplicación de mensajería masiva.
 * Todas las rutas (excepto la página de bienvenida) requieren autenticación.
 */

use App\Http\Controllers\GetSentMessages;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SendMessage;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Ruta principal: Dashboard (requiere autenticación y email verificado)
Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
    // Rutas de gestión del perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ruta para ver historial de mensajes enviados
    Route::get('sent', GetSentMessages::class)->name('sent');

    // Ruta para enviar mensajes masivos
    Route::post('send', SendMessage::class)->name('send');

    // Rutas de gestión de usuarios
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});



require __DIR__.'/auth.php';
