<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use App\Repositories\Interfaces\SchoolIdentityRepositoryInterface;
use Illuminate\Contracts\View\View;

class PublicTrackController extends Controller
{
    public function __construct(
        protected SpmbConfigurationRepositoryInterface $spmbConfigRepo,
        protected SpmbTrackRepositoryInterface $spmbTrackRepo,
        protected SchoolIdentityRepositoryInterface $schoolIdentityRepo
    ) {}

    /**
     * Tampilkan halaman publik pendaftaran (landing page).
     */
    public function index(): View
    {
        $activeConfig = $this->spmbConfigRepo->getActiveConfiguration();
        $tracks = $this->spmbTrackRepo->getActiveTracksWithQuota();
        $schoolIdentity = $this->schoolIdentityRepo->first();

        return view('portal.public.landing', compact('activeConfig', 'tracks', 'schoolIdentity'));
    }
}
