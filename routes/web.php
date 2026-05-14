<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Akademik\AcademicYearController;
use App\Http\Controllers\Admin\Settings\SchoolIdentityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Manajemen Sekolah
|--------------------------------------------------------------------------
*/

// --- Rute Landing ---
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// --- Rute Autentikasi ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// --- Grup Rute Administrator ---
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    // Dashboard Utama
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    /**
     * Modul Akademik
     * Mengelola data tahun ajaran, semester, dll.
     */
    Route::prefix('akademik')->name('akademik.')->group(function () {

        // Tahun Pelajaran
        Route::controller(AcademicYearController::class)
            ->prefix('tahun-pelajaran')
            ->name('tahun-pelajaran.')
            ->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:akademik.tahun-pelajaran.view');
                Route::get('/data', 'getData')->name('data')->middleware('permission:akademik.tahun-pelajaran.view');
                Route::post('/', 'store')->name('store')->middleware('permission:akademik.tahun-pelajaran.create');
                Route::get('/{id}', 'show')->name('show')->middleware('permission:akademik.tahun-pelajaran.edit');
                Route::put('/{id}', 'update')->name('update')->middleware('permission:akademik.tahun-pelajaran.edit');
                Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:akademik.tahun-pelajaran.delete');
                Route::post('/{id}/toggle', 'toggleStatus')->name('toggle')->middleware('permission:akademik.tahun-pelajaran.edit');
            });
    });

    /**
     * Modul Pengaturan (Settings)
     * Mengelola identitas sekolah, RBAC, dan User.
     */
    Route::prefix('settings')->name('settings.')->group(function () {

        // Identitas Sekolah
        Route::controller(SchoolIdentityController::class)
            ->prefix('school')
            ->name('school.')
            ->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:settings.school.view');
                Route::post('/update', 'update')->name('update')->middleware('permission:settings.school.edit');
            });

        // Role Based Access Control (RBAC)
        Route::controller(App\Http\Controllers\Admin\Settings\RoleController::class)
            ->prefix('rbac')
            ->name('rbac.')
            ->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:settings.rbac.view');
                Route::get('/data', 'getData')->name('data')->middleware('permission:settings.rbac.view');
                Route::get('/permissions', 'getPermissions')->name('permissions')->middleware('permission:settings.rbac.view');
                Route::post('/sync-permissions', 'syncPermissions')->name('sync')->middleware('permission:settings.rbac.create');
                Route::get('/create', 'create')->name('create')->middleware('permission:settings.rbac.create');
                Route::post('/', 'store')->name('store')->middleware('permission:settings.rbac.create');
                Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:settings.rbac.edit');
                Route::get('/{id}', 'show')->name('show')->middleware('permission:settings.rbac.edit');
                Route::put('/{id}', 'update')->name('update')->middleware('permission:settings.rbac.edit');
                Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:settings.rbac.delete');
            });

        // User Management
        Route::prefix('user')->name('user.')->controller(\App\Http\Controllers\Admin\Settings\UserController::class)->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission:settings.user.view');
            Route::get('/data', 'getData')->name('data')->middleware('permission:settings.user.view');
            Route::post('/', 'store')->name('store')->middleware('permission:settings.user.create');
            Route::get('/{id}', 'show')->name('show')->middleware('permission:settings.user.view');
            Route::put('/{id}', 'update')->name('update')->middleware('permission:settings.user.edit');
            Route::post('/{id}/reset-password', 'resetPassword')->name('reset-password')->middleware('permission:settings.user.edit');
            Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:settings.user.delete');
        });

    });

});
