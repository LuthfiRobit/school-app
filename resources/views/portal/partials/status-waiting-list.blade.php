<div class="card portal-status-card card-waiting-list border-start-warning h-100 shadow-sm" x-show="status === 'waiting_list'" x-transition>
  <div class="card-body p-4 d-flex flex-column justify-content-between">
    <div>
      <div class="badge badge-warning text-dark mb-3"><i class="fa-solid fa-circle-pause me-1"></i> CADANGAN (WAITING LIST)</div>
      <h3 class="fw-bold text-primary-900 mb-3">Pendaftaran Anda Masuk Daftar Cadangan</h3>
      <p class="text-muted leading-relaxed mb-4">
        Hasil rapat pleno menyatakan bahwa nilai/berkas Anda memenuhi standar kelayakan, namun karena keterbatasan kuota daya tampung jalur <strong>{{ $enrollment && $enrollment->spmbTrack && $enrollment->spmbTrack->trackType ? $enrollment->spmbTrack->trackType->name : '' }}</strong>, Anda dimasukkan ke dalam daftar cadangan.
      </p>

      <div class="row g-3 mb-4">
        <div class="col-sm-6">
          <div class="p-3 bg-light rounded-md border text-center">
            <span class="text-muted d-block small mb-1">Nomor Urut Cadangan Anda:</span>
            <strong class="text-warning fs-3 text-monospace">#{{ $enrollment->waitlist_order ?? '1' }}</strong>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="p-3 bg-light rounded-md border text-center h-100 d-flex flex-column justify-content-center">
            <span class="text-muted d-block small mb-1">Pengumuman Kelulusan Akhir:</span>
            <strong class="text-primary-900 small">
              {{ $enrollment->announcement_visible_at ? $enrollment->announcement_visible_at->translatedFormat('d F Y') : 'Menyusul' }}
            </strong>
          </div>
        </div>
      </div>

      <div class="alert alert-warning d-flex align-items-center mb-4">
        <i class="fa-solid fa-circle-info me-3 fs-4 text-warning"></i>
        <div>
          Status kelulusan cadangan akan otomatis bergeser jika terdapat pendaftar utama yang mengundurkan diri atau tidak melunasi daftar ulang hingga batas waktu yang ditentukan.
        </div>
      </div>
    </div>

    <div>
      <a href="https://wa.me/{{ $schoolIdentity->whatsapp ?? '6281234567890' }}" target="_blank" class="btn btn-outline-secondary px-4 py-2 border-primary text-primary-600">
        <i class="fa-brands fa-whatsapp me-2"></i> Hubungi Customer Service
      </a>
    </div>
  </div>
</div>
