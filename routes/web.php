<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Akademik\AcademicYearController;
use App\Http\Controllers\Admin\Settings\SchoolIdentityController;
use App\Http\Controllers\Applicant\Auth\ApplicantAuthController;
use App\Http\Controllers\Applicant\PublicTrackController;
use App\Http\Controllers\Applicant\ApplicantDashboardController;
use App\Http\Controllers\Applicant\ApplicantEnrollmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Manajemen Sekolah
|--------------------------------------------------------------------------
*/

// === HALAMAN PUBLIK ===
Route::get('/', [PublicTrackController::class, 'index'])->name('public.landing');

// === AUTH APPLICANT (Guest Only) ===
Route::middleware('guest')->group(function () {
    Route::get('/daftar', [ApplicantAuthController::class, 'showRegister'])->name('applicant.register');
    Route::post('/daftar', [ApplicantAuthController::class, 'register'])->name('applicant.register.submit');
    Route::get('/masuk', [ApplicantAuthController::class, 'showLogin'])->name('applicant.login');
    Route::post('/masuk', [ApplicantAuthController::class, 'login'])->name('applicant.login.submit');
    Route::get('/lupa-password', [ApplicantAuthController::class, 'showForgotPassword'])->name('applicant.password.request');
    Route::post('/lupa-password', [ApplicantAuthController::class, 'sendResetLink'])->name('applicant.password.email');
    Route::get('/reset-password/{token}', [ApplicantAuthController::class, 'showResetForm'])->name('applicant.password.reset');
    Route::post('/reset-password', [ApplicantAuthController::class, 'resetPassword'])->name('applicant.password.update');
});

// Logout tidak perlu guest middleware
Route::post('/portal/keluar', [ApplicantAuthController::class, 'logout'])
    ->name('applicant.logout')
    ->middleware('auth');

// === PORTAL APPLICANT (Auth + Role Applicant) ===
Route::prefix('portal')->name('portal.')->middleware(['auth', 'applicant'])->group(function () {
    Route::get('/dashboard', [ApplicantDashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('pendaftaran')->name('enrollment.')->controller(ApplicantEnrollmentController::class)->group(function () {
        Route::get('/pilih-jalur', 'selectTrack')->name('select-track');
        Route::get('/jalur-lain', 'reapplySelect')->name('reapply-select');
        Route::post('/jalur-lain/{trackId}', 'reapplySubmit')->name('reapply-submit');
        Route::get('/daftar/{trackId}', 'showForm')->name('form');
        Route::post('/daftar/{trackId}/draft', 'saveDraft')->name('draft');
        Route::post('/daftar/{trackId}/submit', 'submit')->name('submit');
        Route::get('/{enrollmentId}', 'show')->name('show');
    });

    Route::prefix('pembayaran')->name('payment.')->group(function () {
        Route::get('/{enrollmentId}', function () { return 'Show Payment'; })->name('show');
        Route::post('/{enrollmentId}/upload', function () { return 'Upload Proof'; })->name('upload');
    });

    Route::prefix('daftar-ulang')->name('re-registration.')->group(function () {
        Route::get('/{enrollmentId}', function () { return 'Show Re-registration'; })->name('show');
        Route::post('/{enrollmentId}/upload', function () { return 'Upload Re-registration Proof'; })->name('upload');
        Route::post('/{enrollmentId}/finalisasi', function () { return 'Finalize'; })->name('finalize');
    });

    Route::get('/surat/{enrollmentId}', function () { return 'Download Letter'; })->name('letter.download');
    Route::get('/riwayat', function () { return 'History'; })->name('history.index');
});

// === RUTE AUTENTIKASI ADMIN ===
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
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

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

        // Modul Penilaian & Penetapan Kelulusan (Assessment & Graduation Decision)
        Route::prefix('penilaian')->name('penilaian.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Spmb\AssessmentController::class)->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:spmb.penilaian.view');
                Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.penilaian.view');
                Route::post('/bulk-decision', 'bulkDecision')->name('bulk-decision')->middleware('permission:spmb.penilaian.kelulusan');
                Route::get('/{enrollment}', 'show')->name('show')->middleware('permission:spmb.penilaian.view');
                Route::post('/{enrollment}/upsert', 'upsert')->name('upsert')->middleware('permission:spmb.penilaian.input');
                Route::post('/{enrollment}/decision', 'setDecision')->name('decision')->middleware('permission:spmb.penilaian.kelulusan');
            });
        });

        // Modul Daftar Ulang & Finalisasi
        Route::prefix('daftar-ulang')->name('daftar-ulang.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Spmb\ReRegistrationController::class)->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:spmb.daftarulang.view');
                Route::get('/data', 'getData')->name('data')->middleware('permission:spmb.daftarulang.view');
                Route::get('/{enrollment}', 'show')->name('show')->middleware('permission:spmb.daftarulang.view');
                Route::post('/{enrollment}/finalize', 'finalize')->name('finalize')->middleware('permission:spmb.daftarulang.finalisasi');
                Route::get('/{enrollment}/surat', 'downloadLetter')->name('surat')->middleware('permission:spmb.daftarulang.view');
            });
        });

        // Modul Laporan (Reporting)
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::controller(\App\Http\Controllers\Admin\Spmb\ReportController::class)->group(function () {
                Route::get('/', 'index')->name('index')->middleware('permission:spmb.laporan.view');
                Route::get('/summary', 'summary')->name('summary')->middleware('permission:spmb.laporan.view');
                Route::get('/export/excel', 'exportExcel')->name('export.excel')->middleware('permission:spmb.laporan.export');
                Route::get('/export/pdf', 'exportPdf')->name('export.pdf')->middleware('permission:spmb.laporan.export');
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
