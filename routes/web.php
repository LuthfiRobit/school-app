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

    /**
     * Modul SPMB (Pengelolaan Sistem Penerimaan Peserta Didik Baru)
     * Mengelola data siswa baru, pendaftaran, dll.
     */
    Route::prefix('spmb')->name('spmb.')->group(function () {
        Route::prefix('master')->name('master.')->group(function () {
            // Master Jalur
            Route::controller(\App\Http\Controllers\Admin\Spmb\MasterTrackTypeController::class)
                ->prefix('jalur')
                ->name('jalur.')
                ->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('permission:spmb.master.jalur.view');
                    Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.master.jalur.view');
                    Route::post('/', 'store')->name('store')->middleware('permission:spmb.master.jalur.create');
                    Route::get('/{id}', 'show')->name('show')->middleware('permission:spmb.master.jalur.edit');
                    Route::put('/{id}', 'update')->name('update')->middleware('permission:spmb.master.jalur.edit');
                    Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:spmb.master.jalur.delete');
                    Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status')->middleware('permission:spmb.master.jalur.edit');
                    Route::post('/bulk-status', 'bulkUpdateStatus')->name('bulk-status')->middleware('permission:spmb.master.jalur.edit');
                });

            // Master Jenis Penilaian
            Route::controller(\App\Http\Controllers\Admin\Spmb\MasterAssessmentTypeController::class)
                ->prefix('jenis-penilaian')
                ->name('jenis-penilaian.')
                ->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('permission:spmb.master.jenis-penilaian.view');
                    Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.master.jenis-penilaian.view');
                    Route::post('/', 'store')->name('store')->middleware('permission:spmb.master.jenis-penilaian.create');
                    Route::get('/{id}', 'show')->name('show')->middleware('permission:spmb.master.jenis-penilaian.view');
                    Route::put('/{id}', 'update')->name('update')->middleware('permission:spmb.master.jenis-penilaian.edit');
                    Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:spmb.master.jenis-penilaian.delete');
                    Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status')->middleware('permission:spmb.master.jenis-penilaian.edit');
                    Route::post('/bulk-status', 'bulkUpdateStatus')->name('bulk-status')->middleware('permission:spmb.master.jenis-penilaian.edit');
                });

            // Master Komponen Biaya
            Route::controller(\App\Http\Controllers\Admin\Spmb\MasterFeeComponentController::class)
                ->prefix('komponen-biaya')
                ->name('komponen-biaya.')
                ->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('permission:spmb.master.komponen-biaya.view');
                    Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.master.komponen-biaya.view');
                    Route::post('/', 'store')->name('store')->middleware('permission:spmb.master.komponen-biaya.create');
                    Route::get('/{id}', 'show')->name('show')->middleware('permission:spmb.master.komponen-biaya.view');
                    Route::put('/{id}', 'update')->name('update')->middleware('permission:spmb.master.komponen-biaya.edit');
                    Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:spmb.master.komponen-biaya.delete');
                    Route::post('/{id}/toggle-status', 'toggleStatus')->name('toggle-status')->middleware('permission:spmb.master.komponen-biaya.edit');
                    Route::post('/bulk-status', 'bulkUpdateStatus')->name('bulk-status')->middleware('permission:spmb.master.komponen-biaya.edit');
                });
        });

        // Modul Pendaftar (Enrollment)
        Route::prefix('pendaftar')->name('pendaftar.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Spmb\EnrollmentController::class)->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:spmb.pendaftar.view');
                Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.pendaftar.view');
                Route::get('/{id}', 'show')->name('show')->middleware('permission:spmb.pendaftar.view');
                Route::post('/{id}/status', 'updateStatus')->name('status')->middleware('permission:spmb.pendaftar.status');
                Route::post('/bulk-status', 'bulkStatus')->name('bulk-status')->middleware('permission:spmb.pendaftar.bulk-status');
            });
        });

        // Modul Pembayaran (Payment Verification & Manual Input)
        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Spmb\PaymentController::class)->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:spmb.pembayaran.view');
                Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.pembayaran.view');
                Route::get('/{id}', 'show')->name('show')->middleware('permission:spmb.pembayaran.view');
                Route::post('/{id}/confirm', 'confirm')->name('confirm')->middleware('permission:spmb.pembayaran.verify');
                Route::post('/{id}/reject', 'reject')->name('reject')->middleware('permission:spmb.pembayaran.verify');
                Route::post('/manual', 'storeManual')->name('manual')->middleware('permission:spmb.pembayaran.input');
            });
        });

        // SPMB Configuration Management
        Route::prefix('configurations')->name('configurations.')->group(function () {
            
            // 1. Academic Year Configurations
            Route::controller(\App\Http\Controllers\Admin\Spmb\SpmbConfigurationController::class)->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:spmb.configurations.view');
                Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.configurations.view');
                Route::get('/{academicYear}', 'show')->name('show')->middleware('permission:spmb.configurations.edit');
                Route::put('/{academicYear}', 'update')->name('update')->middleware('permission:spmb.configurations.edit');
                Route::post('/clone', 'clone')->name('clone')->middleware('permission:spmb.configurations.edit');
            });

            // 2. Tracks per Configuration
            Route::prefix('{configuration}/tracks')->name('tracks.')->group(function () {
                
                // 2.1 Core Track Management
                Route::controller(\App\Http\Controllers\Admin\Spmb\SpmbTrackController::class)->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('permission:spmb.configurations.track.view');
                    Route::get('/data', 'data')->name('data')->middleware('permission:spmb.configurations.track.view');
                    Route::post('/', 'store')->name('store')->middleware('permission:spmb.configurations.track.create');
                    Route::get('/{track}', 'show')->name('show')->middleware('permission:spmb.configurations.track.view');
                    Route::put('/{track}', 'update')->name('update')->middleware('permission:spmb.configurations.track.edit');
                    Route::delete('/{track}', 'destroy')->name('destroy')->middleware('permission:spmb.configurations.track.delete');
                    
                    Route::post('/{track}/mappings', 'syncMappings')->name('sync-mappings')->middleware('permission:spmb.configurations.track.mapping.edit');
                });

                // 2.2 Track Fees (Mappings)
                Route::prefix('{track}/fees')->name('fees.')->controller(\App\Http\Controllers\Admin\Spmb\SpmbTrackFeeController::class)->group(function () {
                    Route::get('/master', 'masterData')->name('master')->middleware('permission:spmb.configurations.track.mapping.view');
                    Route::get('/', 'index')->name('index')->middleware('permission:spmb.configurations.track.mapping.view');
                    Route::post('/sync', 'sync')->name('sync')->middleware('permission:spmb.configurations.track.mapping.edit');
                });

                // 2.3 Track Assessments (Mappings)
                Route::prefix('{track}/assessments')->name('assessments.')->controller(\App\Http\Controllers\Admin\Spmb\SpmbTrackAssessmentController::class)->group(function () {
                    Route::get('/master', 'masterData')->name('master')->middleware('permission:spmb.configurations.track.mapping.view');
                    Route::get('/', 'index')->name('index')->middleware('permission:spmb.configurations.track.mapping.view');
                    Route::post('/sync', 'sync')->name('sync')->middleware('permission:spmb.configurations.track.mapping.edit');
                });

                // 2.4 Track Form Fields (Mappings)
                Route::prefix('{track}/form-fields')->name('form-fields.')->controller(\App\Http\Controllers\Admin\Spmb\SpmbTrackFormFieldController::class)->group(function () {
                    Route::get('/', 'index')->name('index')->middleware('permission:spmb.configurations.track.mapping.view');
                    Route::post('/sync', 'sync')->name('sync')->middleware('permission:spmb.configurations.track.mapping.edit');
                });
            });
        });
    });

});
