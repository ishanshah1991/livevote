<?php

declare(strict_types=1);

use App\Http\Controllers\Public\PollController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/polls/{poll}', [PollController::class, 'show'])->name('poll.show');
Route::get('/polls/{poll}/results', [PollController::class, 'results'])->name('poll.results');
Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('poll.vote');
