<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicInteractionController;
use App\Http\Controllers\PublicOfficeController;
use App\Http\Controllers\PublicReviewController;
use App\Http\Controllers\ReviewController;
use App\Models\Regency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/auth-google-redirect', [LoginController::class, 'google_redirect']);
Route::get('/auth-google-callback', [LoginController::class, 'google_callback']);

Route::get('/', [PublicOfficeController::class, 'index'])
    ->name('public.offices.index');

Route::get('/detail/{office}', [PublicOfficeController::class, 'show'])
    ->name('public.offices.show');

Route::get('/detail/{office}/feed', [PublicOfficeController::class, 'feed'])
    ->name('public.office.feed');

// Route::post('/kantor/{office}/reviews', [PublicReviewController::class, 'store'])
//     ->middleware('auth')
//     ->name('public.offices.reviews.store');

Route::post('/kantor/{office}/interactions', [PublicInteractionController::class, 'store'])
    ->middleware('auth')
    ->name('public.offices.interactions.store');

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('kantor', OfficeController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy', 'show'
    ]);

    // Route::resource('review', ReviewController::class)->only([
    //     'index', 'edit', 'update', 'destroy', 'show'
    // ]);

    Route::resource('interaksi', InteractionController::class)->only([
        'index', 'edit', 'update', 'destroy', 'show'
    ]);
});

Route::middleware('auth')->get('/my-profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

Route::middleware('auth')->put('/my-profile', [ProfileController::class, 'update'])
        ->name('profile.update');

Route::post('/login', [LoginController::class, 'login'])->name('login')->middleware('guest');
Route::get('/login', [LoginController::class, 'login_public'])->name('login.public')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/api/regencies', function (Request $request) {
    return Regency::query()
        ->where('province_id', $request->province_id)
        ->orderBy('name')
        ->get(['id', 'name']);
});