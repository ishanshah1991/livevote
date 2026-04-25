<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuthController;
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

    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});
