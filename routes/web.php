<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
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

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.store')->middleware('throttle:5,1');
});

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
    Route::get('/leads/{lead}/csv', [AdminLeadController::class, 'downloadCsv'])->name('leads.csv');
    Route::get('/leads/{lead}/informe', [AdminLeadController::class, 'report'])->name('leads.report');
    Route::post('/leads/{lead}/analyze', [AdminLeadController::class, 'analyze'])->name('leads.analyze');
    Route::patch('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
    Route::patch('/leads/{lead}/notes', [AdminLeadController::class, 'updateNotes'])->name('leads.notes');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
});
