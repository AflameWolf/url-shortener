<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


// Аутентификация (Laravel Breeze)
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}
Route::middleware(['auth'])->get('dashboard', function () {
    return redirect()->route('links.index');
})->name('dashboard');

// Маршруты для авторизованных пользователей
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/links', [LinkController::class, 'index'])->name('links.index');
    Route::get('/links/create', [LinkController::class, 'create'])->name('links.create');
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');
    Route::get('/links/{link}', [LinkController::class, 'show'])->name('links.show');
    Route::get('/links/{link}/edit', [LinkController::class, 'edit'])->name('links.edit');
    Route::put('/links/{link}', [LinkController::class, 'update'])->name('links.update');
    Route::delete('/links/{link}', [LinkController::class, 'destroy'])->name('links.destroy');
    Route::patch('/links/{link}/toggle', [LinkController::class, 'toggleActive'])->name('links.toggle');
    Route::get('/links/{link}/stats', [LinkController::class, 'stats'])->name('links.stats');
});

Route::middleware(['auth'])->get('/', function () {
    return redirect()->route('links.index');
});


//редиректы
Route::get('/{shortCode}', [LinkController::class, 'redirect'])->name('link.redirect');
