<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Game CRUD Routes
// Route::resource('games', GameController::class);
Route::get('games', [GameController::class, 'index'])->name('games.index');
Route::get('games/create', [GameController::class, 'create'])->name('games.create');
Route::post('games', [GameController::class, 'store'])->name('games.store');
Route::get('games/{slug}', [GameController::class, 'show'])->name('games.show');
Route::get('games/{slug}/edit', [GameController::class, 'edit'])->name('games.edit');
Route::put('games/{slug}', [GameController::class, 'update'])->name('games.update');
Route::delete('games/{slug}', [GameController::class, 'destroy'])->name('games.destroy');

// Game Playing Routes
Route::get('games/play/{slug}', [GameController::class, 'play'])->name('games.play');

// Additional Game Routes
Route::get('games/search', [GameController::class, 'search'])->name('games.search');
Route::get('games/genre/{genre}', [GameController::class, 'filterByGenre'])->name('games.genre');
Route::get('games/top-rated', [GameController::class, 'topRated'])->name('games.top-rated');
Route::get('games/recent-releases', [GameController::class, 'recentReleases'])->name('games.recent-releases');
Route::get('games/upcoming', [GameController::class, 'upcomingGames'])->name('games.upcoming');