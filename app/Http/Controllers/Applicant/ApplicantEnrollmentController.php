<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;
use App\Repositories\Interfaces\ApplicantRepositoryInterface;
use App\Services\StateMachineService;
use App\Services\EnrollmentNumberService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicantEnrollmentController extends Controller
{
    public function __construct(
        protected SpmbConfigurationRepositoryInterface $spmbConfigRepo,
        protected SpmbTrackRepositoryInterface $spmbTrackRepo,
        protected ApplicantEnrollmentRepositoryInterface $enrollmentRepo,
        protected ApplicantRepositoryInterface $applicantRepo,
        protected StateMachineService $stateMachineService,
        protected EnrollmentNumberService $enrollmentNumberService
    ) {}

    /**
     * Tampilkan halaman pemilihan jalur pendaftaran.
     */
    public function selectTrack(): View|RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;
        $activeConfig = $this->spmbConfigRepo->getActiveConfiguration();

        if (!$activeConfig) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran Penerimaan Siswa Baru belum dibuka.');
        }

        // Cek pendaftaran yang sudah ada
        $enrollment = $applicant->enrollments()
            ->whereHas('spmbTrack', function ($query) use ($activeConfig) {
                $query->where('spmb_configuration_id', $activeConfig->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($enrollment) {
            if ($enrollment->status->value === 'draft') {
                return redirect()->route('portal.enrollment.form', $enrollment->spmb_track_id);
            }
            if ($enrollment->status->value === 'rejected') {
                return redirect()->route('portal.enrollment.reapply-select');
            }
            return redirect()->route('portal.dashboard')
                ->with('info', 'Anda sudah memiliki pendaftaran aktif.');
        }

        $tracks = $this->spmbTrackRepo->getActiveTracksWithQuota();

        return view('portal.enrollment.select-track', compact('activeConfig', 'tracks'));
    }

    /**
     * Tampilkan formulir pendaftaran dinamis untuk jalur yang dipilih.
     */
    public function showForm(int $trackId): View|RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;
        $activeConfig = $this->spmbConfigRepo->getActiveConfiguration();

        if (!$activeConfig) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran Penerimaan Siswa Baru belum dibuka.');
        }

        $track = $this->spmbTrackRepo->find($trackId, ['*'], ['trackType', 'formFields' => function($q) {
            $q->orderBy('display_order', 'asc');
        }]);

        if (!$track || $track->status !== 'active' || $track->spmb_configuration_id !== $activeConfig->id) {
            return redirect()->route('portal.enrollment.select-track')
                ->with('error', 'Jalur pendaftaran tidak valid atau tidak aktif.');
        }

        // Hitung sisa kuota
        $enrollmentsCount = $track->enrollments()->whereNotIn('status', ['rejected', 'draft'])->count();
        $sisaKuota = max(0, $track->quota - $enrollmentsCount);
        if ($sisaKuota <= 0) {
            return redirect()->route('portal.enrollment.select-track')
                ->with('error', 'Kuota jalur pendaftaran ini sudah penuh.');
        }

        // Cek jika ada pendaftaran aktif di jalur lain
        $otherEnrollment = $applicant->enrollments()
            ->whereHas('spmbTrack', function ($query) use ($activeConfig) {
                $query->where('spmb_configuration_id', $activeConfig->id);
            })
            ->where('spmb_track_id', '!=', $trackId)
            ->where('status', '!=', 'rejected')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($otherEnrollment) {
            if ($otherEnrollment->status->value === 'draft') {
                return redirect()->route('portal.enrollment.form', $otherEnrollment->spmb_track_id);
            }
            return redirect()->route('portal.dashboard')
                ->with('info', 'Anda sudah memiliki pendaftaran aktif di jalur lain.');
        }

        // Buat draft enrollment jika belum ada
        $enrollment = $applicant->enrollments()
            ->where('spmb_track_id', $trackId)
            ->where('status', 'draft')
            ->first();

        if (!$enrollment) {
            $enrollment = $this->enrollmentRepo->create([
                'applicant_id' => $applicant->id,
                'spmb_track_id' => $trackId,
                'status' => 'draft',
                'created_by' => $user->id,
            ]);
        }

        // Pre-fill data form yang sudah disimpan di draft sebelumnya
        $formData = $enrollment->formData->pluck('value', 'field_id')->toArray();
        $formFields = $track->formFields;

        return view('portal.enrollment.form', compact('track', 'enrollment', 'formFields', 'formData', 'applicant'));
    }

    /**
     * Simpan kemajuan formulir sebagai Draf secara berkala (Auto-save).
     */
    public function saveDraft(Request $request, int $trackId): JsonResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        $enrollment = $applicant->enrollments()
            ->where('spmb_track_id', $trackId)
            ->where('status', 'draft')
            ->first();

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Draf pendaftaran tidak ditemukan.'], 404);
        }

        DB::transaction(function () use ($request, $enrollment) {
            $data = $request->input('fields', []);
            foreach ($data as $fieldId => $value) {
                // Lewati jika isinya kosong atau file upload yang belum disubmit final
                if ($value === null || $value === '') {
                    continue;
                }
                
                DB::table('applicant_form_data')->updateOrInsert(
                    [
                        'enrollment_id' => $enrollment->id,
                        'field_id' => $fieldId,
                    ],
                    [
                        'value' => is_array($value) ? json_encode($value) : $value,
                        'updated_at' => now(),
                    ]
                );
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Submit final pendaftaran dan jalankan validasi serta State Machine.
     */
    public function submit(Request $request, int $trackId): RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;
        $activeConfig = $this->spmbConfigRepo->getActiveConfiguration();

        if (!$activeConfig) {
            return redirect()->route('portal.dashboard')->with('error', 'Pendaftaran tidak aktif.');
        }

        $track = $this->spmbTrackRepo->find($trackId, ['*'], ['formFields']);
        if (!$track || $track->status !== 'active') {
            return redirect()->route('portal.enrollment.select-track')->with('error', 'Jalur tidak aktif.');
        }

        $enrollment = $applicant->enrollments()
            ->where('spmb_track_id', $trackId)
            ->where('status', 'draft')
            ->first();

        if (!$enrollment) {
            return redirect()->route('portal.enrollment.select-track')->with('error', 'Draf pendaftaran tidak ditemukan.');
        }

        // Cek sisa kuota dengan lock prevent race condition
        $isQuotaAvailable = DB::transaction(function () use ($track) {
            $usedQuota = DB::table('applicant_enrollments')
                ->where('spmb_track_id', $track->id)
                ->whereNotIn('status', ['rejected', 'draft'])
                ->lockForUpdate()
                ->count();
            return ($track->quota - $usedQuota) > 0;
        });

        if (!$isQuotaAvailable) {
            return redirect()->route('portal.enrollment.select-track')->with('error', 'Maaf, kuota jalur pendaftaran ini sudah penuh.');
        }

        // Jalankan validasi input dinamis
        $rules = [];
        $messages = [];
        $attributes = [];

        foreach ($track->formFields as $field) {
            $fieldName = "fields.{$field->id}";
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->field_type === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($field->field_type === 'date') {
                $fieldRules[] = 'date';
            } elseif ($field->field_type === 'file') {
                // Skip file validation if it was already uploaded and resides in DB
                $hasUploadedFile = DB::table('applicant_form_data')
                    ->where('enrollment_id', $enrollment->id)
                    ->where('field_id', $field->id)
                    ->whereNotNull('value')
                    ->exists();

                if ($hasUploadedFile) {
                    $fieldRules[] = 'nullable';
                } else {
                    $fieldRules[] = 'file';
                    $allowedMime = $field->file_types ?? 'pdf,jpg,jpeg,png';
                    $fieldRules[] = 'mimes:' . $allowedMime;
                    $maxSize = $field->max_file_size ?? 2048; // KB
                    $fieldRules[] = 'max:' . $maxSize;
                }
            } else {
                $fieldRules[] = 'string';
            }

            $rules[$fieldName] = $fieldRules;
            $attributes[$fieldName] = $field->field_label;
        }

        $request->validate($rules, $messages, $attributes);

        // Simpan seluruh data form pendaftaran & file pendukung
        DB::transaction(function () use ($request, $enrollment) {
            $fields = $request->input('fields', []);
            
            foreach ($fields as $fieldId => $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                DB::table('applicant_form_data')->updateOrInsert(
                    ['enrollment_id' => $enrollment->id, 'field_id' => $fieldId],
                    ['value' => is_array($value) ? json_encode($value) : $value, 'updated_at' => now()]
                );
            }

            // Simpan upload berkas file
            if ($request->hasFile('fields')) {
                foreach ($request->file('fields') as $fieldId => $file) {
                    if ($file->isValid()) {
                        $path = $file->store("enrollments/{$enrollment->id}", 'public');
                        DB::table('applicant_form_data')->updateOrInsert(
                            ['enrollment_id' => $enrollment->id, 'field_id' => $fieldId],
                            ['value' => $path, 'updated_at' => now()]
                        );
                    }
                }
            }
        });

        // Transisi status: Draft -> Registered
        $this->stateMachineService->transition($enrollment, \App\Enums\EnrollmentStatus::REGISTERED, 'Formulir pendaftaran berhasil dikirim.');

        // Transisi rute lanjutan berdasarkan nominal pendaftaran & mode pembayaran
        $enrollment->refresh();
        
        if ($track->payment_mode === \App\Enums\PaymentMode::PRE_PAYMENT && $track->registration_fee > 0) {
            $this->stateMachineService->transition($enrollment, \App\Enums\EnrollmentStatus::WAITING_PAYMENT_REG, 'Menunggu pembayaran biaya pendaftaran.');
        } else {
            $this->stateMachineService->transition($enrollment, \App\Enums\EnrollmentStatus::VERIFIED_REG, 'Pendaftaran langsung aktif tanpa biaya pendaftaran.');
        }

        return redirect()->route('portal.dashboard')
            ->with('success', 'Pendaftaran Anda berhasil dikirim!');
    }

    /**
     * Tampilkan ringkasan pendaftaran.
     */
    public function show(int $enrollmentId): View|RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        $enrollment = $this->enrollmentRepo->find($enrollmentId, ['*'], ['spmbTrack.trackType', 'invoices']);

        if (!$enrollment || $enrollment->applicant_id !== $applicant->id) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $formData = $enrollment->formData()->with('field')->get();

        return view('portal.enrollment.show', compact('enrollment', 'formData'));
    }

    /**
     * Pilih jalur pendaftaran baru (Re-Apply) setelah ditolak (REJECTED).
     */
    public function reapplySelect(): View|RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;
        $activeConfig = $this->spmbConfigRepo->getActiveConfiguration();

        if (!$activeConfig) {
            return redirect()->route('portal.dashboard')->with('error', 'Pendaftaran tidak aktif.');
        }

        // Pastikan pendaftaran terakhir berstatus rejected
        $lastEnrollment = $applicant->enrollments()
            ->whereHas('spmbTrack', function ($query) use ($activeConfig) {
                $query->where('spmb_configuration_id', $activeConfig->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastEnrollment || $lastEnrollment->status->value !== 'rejected') {
            return redirect()->route('portal.dashboard')->with('error', 'Aksi re-apply tidak diizinkan.');
        }

        $tracks = $this->spmbTrackRepo->getActiveTracksWithQuota();
        
        // Dapatkan ID jalur pendaftaran yang pernah didaftar calon siswa
        $usedTrackIds = $applicant->enrollments()
            ->whereHas('spmbTrack', function ($query) use ($activeConfig) {
                $query->where('spmb_configuration_id', $activeConfig->id);
            })
            ->pluck('spmb_track_id')
            ->toArray();

        $rejectedLog = $lastEnrollment->statusLogs()
            ->where('to_status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('portal.enrollment.reapply', compact('activeConfig', 'tracks', 'usedTrackIds', 'lastEnrollment', 'rejectedLog'));
    }

    /**
     * Proses pengajuan pendaftaran ke jalur baru setelah ditolak.
     */
    public function reapplySubmit(Request $request, int $trackId): RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;
        $activeConfig = $this->spmbConfigRepo->getActiveConfiguration();

        if (!$activeConfig) {
            return redirect()->route('portal.dashboard')->with('error', 'Pendaftaran tidak aktif.');
        }

        // Pastikan status terakhir rejected
        $lastEnrollment = $applicant->enrollments()
            ->whereHas('spmbTrack', function ($query) use ($activeConfig) {
                $query->where('spmb_configuration_id', $activeConfig->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastEnrollment || $lastEnrollment->status->value !== 'rejected') {
            return redirect()->route('portal.dashboard')->with('error', 'Aksi re-apply tidak diizinkan.');
        }

        // Pastikan belum pernah mendaftar di jalur baru pilihan ini
        $usedTrackIds = $applicant->enrollments()
            ->whereHas('spmbTrack', function ($query) use ($activeConfig) {
                $query->where('spmb_configuration_id', $activeConfig->id);
            })
            ->pluck('spmb_track_id')
            ->toArray();

        if (in_array($trackId, $usedTrackIds)) {
            return redirect()->route('portal.enrollment.reapply-select')
                ->with('error', 'Anda tidak dapat memilih kembali jalur pendaftaran yang pernah dicoba.');
        }

        $track = $this->spmbTrackRepo->find($trackId);
        if (!$track || $track->status !== 'active' || $track->spmb_configuration_id !== $activeConfig->id) {
            return redirect()->route('portal.enrollment.reapply-select')
                ->with('error', 'Jalur pendaftaran tidak aktif.');
        }

        // Buat draf baru
        $enrollment = $this->enrollmentRepo->create([
            'applicant_id' => $applicant->id,
            'spmb_track_id' => $trackId,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        return redirect()->route('portal.enrollment.form', $trackId)
            ->with('success', 'Silakan isi berkas formulir untuk jalur pilihan baru Anda.');
    }
}
