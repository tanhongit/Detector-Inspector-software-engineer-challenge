<?php

use App\Http\Controllers\WikipediaGraphController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WikipediaGraphController::class, 'index'])->name('wikipedia-graph.index');
Route::post('/generate', [WikipediaGraphController::class, 'generate'])->name('wikipedia-graph.generate');
