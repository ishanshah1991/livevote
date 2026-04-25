<?php

declare(strict_types=1);

use App\Http\Controllers\Public\PollController;
use App\Http\Controllers\Public\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/polls/{poll}', [PollController::class, 'show'])->name('poll.show');
Route::get('/polls/{poll}/results', [PollController::class, 'results'])->name('poll.results');

Route::middleware(['throttle:vote'])->group(function (): void {
    Route::post('/polls/{poll}/vote', [VoteController::class, 'store'])->name('poll.vote');
});
