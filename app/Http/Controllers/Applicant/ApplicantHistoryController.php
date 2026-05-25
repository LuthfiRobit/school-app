<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ApplicantHistoryController extends Controller
{
    public function __construct(
        protected ApplicantEnrollmentRepositoryInterface $enrollmentRepo
    ) {}

    /**
     * Tampilkan riwayat seluruh pendaftaran yang pernah diajukan oleh calon siswa.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        if (!$applicant) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Profil calon siswa belum lengkap.');
        }

        // Ambil semua pendaftaran milik applicant menggunakan Repository Pattern
        $enrollments = $this->enrollmentRepo->query([
            'spmbTrack.trackType',
            'spmbTrack.spmbConfiguration.academicYear'
        ])
        ->where('applicant_id', $applicant->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('portal.history.index', compact('enrollments', 'applicant'));
    }
}
