<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests; // <-- Ajoute cet import en haut

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class, // <-- Ajoute cette ligne ici
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
    Route::get('/admin/reservations', function () {
    $reservations = \App\Models\Reservation::with('room')
        ->orderBy('created_at', 'desc')
        ->get();

    return Inertia::render('Admin/Reservations', [
        'reservations' => $reservations
    ]);
})->name('admin.reservations');