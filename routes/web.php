<?php

use App\Http\Controllers\LocationController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// SEMENTARA: masuk otomatis sebagai owner seeder. Hapus bila halaman login sudah ada.
if (app()->isLocal()) {
    Route::get('/dev/masuk', function () {
        $owner = User::whereRaw('lower(email) = ?', ['owner@damora.test'])->firstOrFail();

        Auth::login($owner);
        request()->session()->regenerate();

        return redirect('/');
    })->name('dev.login');
}

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => Inertia::render('welcome'));

    Route::resource('lokasi-kantor', LocationController::class)
        ->parameters(['lokasi-kantor' => 'location'])
        ->names('locations')
        ->only(['index', 'store', 'update']);

    Route::patch('lokasi-kantor/{location}/status', [LocationController::class, 'updateStatus'])
        ->name('locations.status');
});
