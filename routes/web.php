<?php

use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/calculadora', [PageController::class, 'calculator'])->name('calculator');

Route::post('/calculadora', [CalculatorController::class, 'calculate'])->name('calculator.calculate')->middleware('throttle:10,1');

Route::post('/lead', [LeadController::class, 'store'])->name('lead.store')->middleware('throttle:5,1');

Route::get('/gracias', [PageController::class, 'thanks'])->name('thanks');

Route::get('/como-funciona', [PageController::class, 'howItWorks'])->name('how-it-works');

Route::get('/precios', [PageController::class, 'pricing'])->name('pricing');

Route::get('/aviso-legal', [PageController::class, 'legal'])->name('legal');

Route::get('/privacidad', [PageController::class, 'privacy'])->name('privacy');

Route::get('/cookies', [PageController::class, 'cookies'])->name('cookies');
