<?php

use App\Http\Controllers\Controller; // Controlador principal para irmãos
use App\Http\Controllers\VisitorController; // Controlador para visitantes
use App\Http\Controllers\ReportController;// Rota presença

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
Route::post('/brother/register-presence', [Controller::class, 'registerBrotherPresence']);

// Rota irmão do visitante 
//Route::post('/register-visitor', [VisitorController::class, 'register']);
Route::post('/register-visitor', [VisitorController::class, 'register'])->name('register-visitor');
Route::post('/verify-visitor', [VisitorController::class, 'verify']);
Route::post('/visitor/register-presence', [VisitorController::class, 'registerVisitorPresence']);

// Rota presença
Route::get('/relatorio-presencas', [ReportController::class, 'showReport'])->name('presence.report');
//Rota para exportar o PDF
Route::get('/relatorio-presencas/pdf', [ReportController::class, 'exportPdf'])->name('presence.report.pdf');
//Rota para imprimir o PDF
Route::get('/presence/report/print', [ReportController::class, 'print'])->name('presence.report.print');

// Rota para login do chanceler
Route::post('/login-chancellor', [ReportController::class, 'loginChancellor'])->name('login.chancellor');


Route::get('/visitor', function () {
    return view('visitor-form');
})->name('visitor-page');