<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CantiereController;
use App\Http\Controllers\AttivitaController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Cantieri
Route::resource('cantieri', CantiereController::class)->parameters([
    'cantieri' => 'cantiere'
]);

// Attività
Route::post('cantieri/{cantiere}/attivita/assegna', [AttivitaController::class, 'assegna'])
    ->name('attivita.assegna');
Route::patch('cantieri/{cantiere}/attivita/{attivita}/stato', [AttivitaController::class, 'updateStato'])
    ->name('attivita.updateStato');
Route::delete('cantieri/{cantiere}/attivita/{attivita}', [AttivitaController::class, 'rimuovi'])
    ->name('attivita.rimuovi');
Route::patch('cantiere-attivita/{cantiereAttivita}/passo/{passoId}', [AttivitaController::class, 'completaPasso'])
    ->name('attivita.completaPasso');
