<?php

use App\Http\Controllers\Controller; // Controlador principal
use Illuminate\Support\Facades\Route;

// Redireciona a rota inicial para a página de seleção de usuário
Route::get('/', function () {
    return redirect('select-user');
})->name('select-user');

// Página de seleção de usuário
Route::get('/select-user', function () {
    return view('select-user');
})->name('select-user');

// Rota para buscar dados do irmão do quadro pelo número SIM
Route::post('/brother-data', [Controller::class, 'getBrotherData']);

// Rota para registrar presença
Route::post('/register-presence', [Controller::class, 'registerPresence']);

// As rotas abaixo estão desativadas por não serem necessárias no momento
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';
