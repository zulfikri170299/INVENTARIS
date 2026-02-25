<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SenjataController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\AlsusController;
use App\Http\Controllers\AlsintorController;
use App\Http\Controllers\SatkerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Inventaris Modules
    Route::post('senjata/import', [SenjataController::class, 'import'])->name('senjata.import');
    Route::get('senjata/download-template', [SenjataController::class, 'downloadTemplate'])->name('senjata.download-template');
    Route::get('senjata/export-pdf', [SenjataController::class, 'exportPdf'])->name('senjata.export-pdf');
    Route::resource('senjata', SenjataController::class);

    Route::post('kendaraan/import', [KendaraanController::class, 'import'])->name('kendaraan.import');
    Route::get('kendaraan/download-template', [KendaraanController::class, 'downloadTemplate'])->name('kendaraan.download-template');
    Route::get('kendaraan/export-pdf', [KendaraanController::class, 'exportPdf'])->name('kendaraan.export-pdf');
    Route::resource('kendaraan', KendaraanController::class);

    Route::post('alsus/import', [AlsusController::class, 'import'])->name('alsus.import');
    Route::get('alsus/download-template', [AlsusController::class, 'downloadTemplate'])->name('alsus.download-template');
    Route::get('alsus/export-pdf', [AlsusController::class, 'exportPdf'])->name('alsus.export-pdf');
    Route::resource('alsus', AlsusController::class);

    Route::post('alsintor/import', [AlsintorController::class, 'import'])->name('alsintor.import');
    Route::get('alsintor/download-template', [AlsintorController::class, 'downloadTemplate'])->name('alsintor.download-template');
    Route::get('alsintor/export-pdf', [AlsintorController::class, 'exportPdf'])->name('alsintor.export-pdf');
    Route::resource('alsintor', AlsintorController::class);

    // Master Data
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('users', UserController::class);
    Route::resource('satkers', SatkerController::class);

    // Conflict Resolution Routes
    Route::post('/senjata/confirm-import', [SenjataController::class, 'confirmImport'])->name('senjata.confirm-import');
    Route::post('/kendaraan/confirm-import', [KendaraanController::class, 'confirmImport'])->name('kendaraan.confirm-import');
    Route::post('/alsus/confirm-import', [AlsusController::class, 'confirmImport'])->name('alsus.confirm-import');
    Route::post('/alsintor/confirm-import', [AlsintorController::class, 'confirmImport'])->name('alsintor.confirm-import');

    // Chat Routes
    Route::get('/chat/users', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.users');
    Route::get('/chat/messages/{receiverId}', [\App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/status', [\App\Http\Controllers\ChatController::class, 'updateStatus'])->name('chat.status');
});

require __DIR__.'/auth.php';
