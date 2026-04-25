<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PollController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| All routes here are loaded under the URI prefix "/admin" and the route
| name prefix "admin.". The "web" middleware group is applied automatically
| by bootstrap/app.php so sessions and CSRF protection are available.
|
*/

Route::prefix('admin')->name('admin.')->group(function (): void {

    // Guest-only auth routes
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Authenticated admin routes
    Route::middleware('admin.auth')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('polls', [PollController::class, 'index'])->name('polls.index');

        Route::get('polls/create', [PollController::class, 'create'])->name('polls.create');
    });
});
