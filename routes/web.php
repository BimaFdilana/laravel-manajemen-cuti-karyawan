<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicCutiController;
use App\Http\Controllers\KaryawanController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PublicCutiController::class, 'index'])->name('cuti.public');

Route::middleware(['auth'])->group(function () {
    Route::get('home', [DashboardController::class, 'index'])->name('home');

    Route::resource('cuti', CutiController::class)->except(['show']);

    Route::resource('ruangan', RuanganController::class);

    Route::resource('karyawan', KaryawanController::class);

    Route::delete('notifications/{id}', [CutiController::class, 'destroyNotification'])->name('notifications.destroy');

    Route::get('notifications', [CutiController::class, 'showAllNotifications'])->name('notifications.index');

    Route::post('notifications/mark-as-read', [CutiController::class, 'markAllNotificationsAsRead'])->name('notifications.markAsRead');

    Route::get('cuti/export', [CutiController::class, 'export'])->name('cuti.export');

    Route::prefix('notifications')->name('notifications.')->group(function () {

        Route::get('/', [CutiController::class, 'showAllNotifications'])->name('index');

        Route::post('/mark-all-as-read', [CutiController::class, 'markAllNotificationsAsRead'])->name('markAllAsRead');

        Route::delete('/{id}', [CutiController::class, 'destroyNotification'])->name('destroy');
    });

    Route::post('karyawan/import', [KaryawanController::class, 'importExcel'])->name('karyawan.import');
    Route::get('karyawan/template', [KaryawanController::class, 'downloadTemplate'])->name('karyawan.template');



Route::post('/notifications/{id}/mark-as-read', [CutiController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::get('register', function () {
        return view('pages.auth.auth-register');
    })->name('register');
});