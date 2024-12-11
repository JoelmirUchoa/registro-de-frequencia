<?php

use App\Http\Controllers\Controller; // Controlador principal para irmãos
use App\Http\Controllers\VisitorController; // Controlador para visitantes
use Illuminate\Support\Facades\Route;

// Redireciona a rota inicial para a página de seleção de usuário
Route::get('/', function () {
    return redirect('select-user');
})->name('select-user');

// Página de seleção de usuário
Route::get('/select-user', function () {
    return view('select-user');
})->name('select-user');

// Rota irmão do quadro 
Route::post('/brother-data', [Controller::class, 'getBrotherData']);
//Route::post('/register-presence', [Controller::class, 'registerPresence']);
Route::post('/brother/register-presence', [Controller::class, 'registerBrotherPresence']);

// Rota irmão do visitante 
//Route::post('/register-visitor', [VisitorController::class, 'register']);
Route::post('/verify-visitor', [VisitorController::class, 'verify']);
Route::post('/visitor/register-presence', [VisitorController::class, 'registerVisitorPresence']);

Route::get('/visitor', function () {
    return view('visitor-form');
})->name('visitor-page');