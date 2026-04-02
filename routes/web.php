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
    Route::get('activity-logs/export-pdf', [\App\Http\Controllers\ActivityLogController::class, 'exportPdf'])->name('activity-logs.export-pdf');
    Route::get('activity-logs/export-excel', [\App\Http\Controllers\ActivityLogController::class, 'exportExcel'])->name('activity-logs.export-excel');
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::post('senjata/{senjata}/return-amunisi', [SenjataController::class, 'returnAmunisi'])->name('senjata.return-amunisi');
    Route::post('senjata/{senjata}/transfer', [SenjataController::class, 'transfer'])->name('senjata.transfer');
    Route::get('senjata/laporan-ringkas', [SenjataController::class, 'laporanRingkas'])->name('senjata.laporan-ringkas');
    Route::get('senjata-pembawa', [SenjataController::class, 'pembawa'])->name('senjata.pembawa');
    Route::resource('senjata', SenjataController::class);

    Route::post('kendaraan/import', [KendaraanController::class, 'import'])->name('kendaraan.import');
    Route::get('kendaraan/download-template', [KendaraanController::class, 'downloadTemplate'])->name('kendaraan.download-template');
    Route::get('kendaraan/export-pdf', [KendaraanController::class, 'exportPdf'])->name('kendaraan.export-pdf');
    Route::get('kendaraan/export-excel', [KendaraanController::class, 'exportExcel'])->name('kendaraan.export-excel');
    Route::post('kendaraan/{kendaraan}/transfer', [KendaraanController::class, 'transfer'])->name('kendaraan.transfer');
    Route::get('kendaraan/laporan-ringkas', [KendaraanController::class, 'laporanRingkas'])->name('kendaraan.laporan-ringkas');
    Route::resource('kendaraan', KendaraanController::class);

    Route::post('alsus/import', [AlsusController::class, 'import'])->name('alsus.import');
    Route::get('alsus/download-template', [AlsusController::class, 'downloadTemplate'])->name('alsus.download-template');
    Route::get('alsus/export-pdf', [AlsusController::class, 'exportPdf'])->name('alsus.export-pdf');
    Route::get('alsus/export-excel', [AlsusController::class, 'exportExcel'])->name('alsus.export-excel');
    Route::get('alsus-alsintor/laporan-ringkas', [AlsusController::class, 'laporanRingkas'])->name('alsus-alsintor.laporan-ringkas');
    Route::get('alsus-alsintor/export-summary', [AlsusController::class, 'exportSummary'])->name('alsus-alsintor.export-summary');
    Route::post('alsus/{alsu}/transfer', [AlsusController::class, 'transfer'])->name('alsus.transfer');
    Route::resource('alsus', AlsusController::class);

    Route::post('alsintor/import', [AlsintorController::class, 'import'])->name('alsintor.import');
    Route::get('alsintor/download-template', [AlsintorController::class, 'downloadTemplate'])->name('alsintor.download-template');
    Route::get('alsintor/export-pdf', [AlsintorController::class, 'exportPdf'])->name('alsintor.export-pdf');
    Route::get('alsintor/export-excel', [AlsintorController::class, 'exportExcel'])->name('alsintor.export-excel');
    Route::post('alsintor/{alsintor}/transfer', [AlsintorController::class, 'transfer'])->name('alsintor.transfer');
    Route::resource('alsintor', AlsintorController::class);

    Route::post('amunisi/import', [AmunisiController::class, 'import'])->name('amunisi.import');
    Route::get('amunisi/download-template', [AmunisiController::class, 'downloadTemplate'])->name('amunisi.download-template');
    Route::post('amunisi/confirm-import', [AmunisiController::class, 'confirmImport'])->name('amunisi.confirm-import');
    Route::get('amunisi/history', [AmunisiHistoryController::class, 'index'])->name('amunisi-history.index');
    Route::get('amunisi/export-pdf', [AmunisiController::class, 'exportPdf'])->name('amunisi.export-pdf');
    Route::get('amunisi/export-excel', [AmunisiController::class, 'exportExcel'])->name('amunisi.export-excel');
    Route::post('amunisi/{amunisi}/transfer', [AmunisiController::class, 'transfer'])->name('amunisi.transfer');
    Route::resource('amunisi', AmunisiController::class);

    // Master Data
    Route::get('users/export-pdf', [UserController::class, 'exportPdf'])->name('users.export-pdf');
    Route::get('users/export-excel', [UserController::class, 'exportExcel'])->name('users.export-excel');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('users', UserController::class);

    Route::get('satkers/export-pdf', [SatkerController::class, 'exportPdf'])->name('satkers.export-pdf');
    Route::get('satkers/export-excel', [SatkerController::class, 'exportExcel'])->name('satkers.export-excel');
    Route::post('satkers/import', [SatkerController::class, 'import'])->name('satkers.import');
    Route::get('satkers/download-template', [SatkerController::class, 'downloadTemplate'])->name('satkers.download-template');
    Route::post('satkers/confirm-import', [SatkerController::class, 'confirmImport'])->name('satkers.confirm-import');
    Route::resource('satkers', SatkerController::class);

    // Conflict Resolution Routes
    Route::post('/senjata/confirm-import', [SenjataController::class, 'confirmImport'])->name('senjata.confirm-import');
    Route::post('/kendaraan/confirm-import', [KendaraanController::class, 'confirmImport'])->name('kendaraan.confirm-import');
    Route::post('/alsus/confirm-import', [AlsusController::class, 'confirmImport'])->name('alsus.confirm-import');
    Route::post('/alsintor/confirm-import', [AlsintorController::class, 'confirmImport'])->name('alsintor.confirm-import');

    // Bulk Delete Routes
    Route::post('senjata/bulk-delete', [SenjataController::class, 'bulkDelete'])->name('senjata.bulk-delete');
    Route::post('kendaraan/bulk-delete', [KendaraanController::class, 'bulkDelete'])->name('kendaraan.bulk-delete');
    Route::post('alsus/bulk-delete', [AlsusController::class, 'bulkDelete'])->name('alsus.bulk-delete');
    Route::post('alsintor/bulk-delete', [AlsintorController::class, 'bulkDelete'])->name('alsintor.bulk-delete');
    Route::post('amunisi/bulk-delete', [AmunisiController::class, 'bulkDelete'])->name('amunisi.bulk-delete');
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::post('satkers/bulk-delete', [SatkerController::class, 'bulkDelete'])->name('satkers.bulk-delete');

    // Chat Routes
    Route::get('/chat/users', [\App\Http\Controllers\ChatController::class, 'index'])->name('chat.users');
    Route::get('/chat/messages/{receiverId}', [\App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/status', [\App\Http\Controllers\ChatController::class, 'updateStatus'])->name('chat.status');



    // Notifikasi Lonceng
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

require __DIR__.'/auth.php';
