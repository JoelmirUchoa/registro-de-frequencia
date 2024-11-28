<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;

Route::get('/', function () {
    return redirect('/login');
});

// Para o painel de usuário
Route::get('/dashboard', function () {
    return view('user-main');
})->middleware(['auth', 'verified'])->name('dashboard');

// Para selecionar o tipo de usuário
Route::middleware('auth')->get('/select-user', function () {
    return view('auth.select-user');
})->name('select-user');

// Para a criação de convidado
// Route::get('/guest/create', [GuestController::class, 'create'])->name('guest.create');
Route::middleware('auth')->get('/guest/create', [GuestController::class, 'create'])->name('guest.create');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
