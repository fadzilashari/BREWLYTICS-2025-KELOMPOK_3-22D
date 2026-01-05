<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForecastController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return redirect('/login');
});

// Dashboard using Filament Admin Panel

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test-fastapi', function () {
    $response = Http::get('http://127.0.0.1:8025/ping');

    return [
        'laravel-status' => 'ok',
        'fastapi-status' => $response->json(),
    ];
});

Route::get('/test-fastapi-post', function () {
    $response = Http::post('http://127.0.0.1:8025/echo', [
        'product_id' => 10,
        'sales' => [10, 12, 15]
    ]);

    return $response->json();
});

Route::get('/test-fastapi-db', function () {
    return Http::get('http://127.0.0.1:8025/test-db')->json();
});

Route::get('/forecast/{id}/db', [ForecastController::class, 'generateFromDb']);
Route::get('/forecast/{id}/pdf', [ForecastController::class, 'downloadPdf']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
