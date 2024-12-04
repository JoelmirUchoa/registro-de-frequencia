<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller; //rota para buscar os dados pelo número SIM


// Rota inicial redireciona para 'select-user'
Route::get('/', function () {
    return redirect('select-user');
})->name('select-user');

// Rota para a página de seleção de usuário
Route::get('/select-user', function () {
    return view('select-user');
})->name('select-user');

//rota para buscar os dados pelo número SIM
// Rota para buscar dados do irmão do quadro
Route::post('/brother-data', [Controller::class, 'getBrotherData']);
// Rota para registrar presença
Route::post('/register-presence', [Controller::class, 'registerPresence']);
Route::post('/get-brother-data', [Controller::class, 'getBrotherData']);


// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';
