<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SenjataController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\AlsusController;
use App\Http\Controllers\AlsintorController;
use App\Http\Controllers\SatkerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AmunisiController;
use App\Http\Controllers\AmunisiHistoryController;
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
    Route::get('senjata/export-excel', [SenjataController::class, 'exportExcel'])->name('senjata.export-excel');
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::post('senjata/{senjata}/return-amunisi', [SenjataController::class, 'returnAmunisi'])->name('senjata.return-amunisi');
    Route::resource('senjata', SenjataController::class);

    Route::post('kendaraan/import', [KendaraanController::class, 'import'])->name('kendaraan.import');
    Route::get('kendaraan/download-template', [KendaraanController::class, 'downloadTemplate'])->name('kendaraan.download-template');
    Route::get('kendaraan/export-pdf', [KendaraanController::class, 'exportPdf'])->name('kendaraan.export-pdf');
    Route::get('kendaraan/export-excel', [KendaraanController::class, 'exportExcel'])->name('kendaraan.export-excel');
    Route::resource('kendaraan', KendaraanController::class);

    Route::post('alsus/import', [AlsusController::class, 'import'])->name('alsus.import');
    Route::get('alsus/download-template', [AlsusController::class, 'downloadTemplate'])->name('alsus.download-template');
    Route::get('alsus/export-pdf', [AlsusController::class, 'exportPdf'])->name('alsus.export-pdf');
    Route::get('alsus/export-excel', [AlsusController::class, 'exportExcel'])->name('alsus.export-excel');
    Route::resource('alsus', AlsusController::class);

    Route::post('alsintor/import', [AlsintorController::class, 'import'])->name('alsintor.import');
    Route::get('alsintor/download-template', [AlsintorController::class, 'downloadTemplate'])->name('alsintor.download-template');
    Route::get('alsintor/export-pdf', [AlsintorController::class, 'exportPdf'])->name('alsintor.export-pdf');
    Route::get('alsintor/export-excel', [AlsintorController::class, 'exportExcel'])->name('alsintor.export-excel');
    Route::resource('alsintor', AlsintorController::class);

    Route::post('amunisi/import', [AmunisiController::class, 'import'])->name('amunisi.import');
    Route::get('amunisi/download-template', [AmunisiController::class, 'downloadTemplate'])->name('amunisi.download-template');
    Route::post('amunisi/confirm-import', [AmunisiController::class, 'confirmImport'])->name('amunisi.confirm-import');
    Route::get('amunisi/history', [AmunisiHistoryController::class, 'index'])->name('amunisi-history.index');
    Route::get('amunisi/export-pdf', [AmunisiController::class, 'exportPdf'])->name('amunisi.export-pdf');
    Route::get('amunisi/export-excel', [AmunisiController::class, 'exportExcel'])->name('amunisi.export-excel');
    Route::resource('amunisi', AmunisiController::class);

    // Master Data
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('users', UserController::class);
    Route::post('satkers/import', [SatkerController::class, 'import'])->name('satkers.import');
    Route::get('satkers/download-template', [SatkerController::class, 'downloadTemplate'])->name('satkers.download-template');
    Route::post('satkers/confirm-import', [SatkerController::class, 'confirmImport'])->name('satkers.confirm-import');
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
