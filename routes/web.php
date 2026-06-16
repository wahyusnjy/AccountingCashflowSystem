<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\AuditLogController;

// --- GUEST / AUTHENTICATION ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- SHARED ACCESSIBLE PAGES ---
    Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [ReportController::class, 'dashboard']);

    Route::get('/pemasukan', [ReportController::class, 'pemasukan'])->name('pemasukan.index');
    Route::post('/pemasukan', [ReportController::class, 'storePemasukan'])->name('pemasukan.store');

    Route::get('/pengeluaran', [ReportController::class, 'pengeluaran'])->name('pengeluaran.index');
    Route::post('/pengeluaran', [ReportController::class, 'storePengeluaran'])->name('pengeluaran.store');

    Route::get('/accounting', [ReportController::class, 'accounting'])->name('accounting.index');
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-gl-pdf', [ReportController::class, 'exportGLPdf'])->name('export.gl.pdf');
    Route::get('/export-tb-excel', [ReportController::class, 'exportTBExcel'])->name('export.tb.excel');
    Route::get('/export-tb-pdf', [ReportController::class, 'exportTBPdf'])->name('export.tb.pdf');
    Route::get('/export-lr-excel', [ReportController::class, 'exportLRExcel'])->name('export.lr.excel');
    Route::get('/export-lr-pdf', [ReportController::class, 'exportLRPdf'])->name('export.lr.pdf');
    Route::get('/export-lak-excel', [ReportController::class, 'exportLAKExcel'])->name('export.lak.excel');
    Route::get('/export-lak-pdf', [ReportController::class, 'exportLAKPdf'])->name('export.lak.pdf');
    Route::get('/export-slip-gaji-blank', [ReportController::class, 'exportSlipGajiBlank'])->name('export.slip.gaji.blank');
    Route::get('/export-slip-gaji/{transaction}', [ReportController::class, 'exportSlipGaji'])->name('export.slip.gaji');
    
    Route::resource('students', \App\Http\Controllers\StudentController::class);
    Route::resource('accounts', \App\Http\Controllers\AccountController::class);
    
    // System logs and device audit trail
    Route::get('/activity-logs', [AuditLogController::class, 'index'])->name('audit.index');
});

