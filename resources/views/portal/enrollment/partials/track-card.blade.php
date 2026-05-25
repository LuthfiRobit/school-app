<div class="col-md-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
  <div class="card track-selection-card shadow-sm border-0 d-flex flex-column h-100"
    :class="isTrackDisabled(track) && 'disabled'">
    
    <div class="card-body p-4 d-flex flex-column justify-content-between h-100">
      <div>
        <!-- Top Badge & Header -->
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="track-icon-wrapper" :style="'background: ' + track.bgGradient + '; color: ' + track.iconColor">
            <span x-text="track.icon"></span>
          </div>
          
          <div class="d-flex flex-column align-items-end">
            <!-- Re-Apply Recommendation Badge -->
            <template x-if="getReApplyRecommendation(track)">
              <span class="badge bg-success text-white mb-1"><i class="fa-solid fa-star me-1 text-warning"></i> Rekomendasi Re-Apply</span>
            </template>

            <!-- Quota Badges -->
            <template x-if="isQuotaFull(track)">
              <span class="badge bg-danger text-white">Kuota Penuh</span>
            </template>
            <template x-if="!isQuotaFull(track)">
              <span class="badge bg-light text-primary-800 border border-primary border-opacity-25" 
                x-text="'Sisa ' + getQuotaLeft(track) + ' Kuota'"></span>
            </template>
          </div>
        </div>

        <!-- Title & Tags -->
        <h5 class="fw-bold text-primary-900 mb-1" x-text="track.name"></h5>
        <div class="d-flex flex-wrap gap-1 mb-3">
          <template x-for="tag in track.tags">
            <span class="tag-pill bg-light text-muted" x-text="tag"></span>
          </template>
        </div>

        <!-- Description -->
        <p class="text-muted small leading-relaxed mb-4" x-text="track.description"></p>

        <!-- Requirements Checklist -->
        <div class="p-3 bg-light rounded-md mb-4 border">
          <h6 class="fw-bold text-primary-800 fs-xs text-uppercase tracking-wider mb-2"><i class="fa-solid fa-clipboard-list me-1"></i> Syarat Utama:</h6>
          <ul class="requirements-check-list p-0 m-0">
            <template x-for="req in track.requirements">
              <li class="text-muted" x-text="req"></li>
            </template>
          </ul>
        </div>
      </div>

      <!-- Price & Selection Actions -->
      <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted d-block small">Biaya Pendaftaran:</span>
          <strong class="text-primary-950 text-lg" x-text="formatCurrency(track.fee)"></strong>
        </div>
        <div>
          <!-- Active Button -->
          <button class="btn btn-primary px-4 py-2" 
            x-show="!isTrackDisabled(track)"
            @click="selectTrack(track)">
            Pilih Jalur <i class="fa-solid fa-chevron-right ms-2 fs-xs"></i>
          </button>

          <!-- Disabled Warning Button -->
          <button class="btn btn-secondary bg-light text-muted border-0 cursor-pointer px-3 py-2 text-start" 
            style="max-width: 180px; font-size:0.75rem;"
            x-show="isTrackDisabled(track)"
            @click="toastr.warning(getDisabledReason(track))"
            title="Jalur Tidak Tersedia">
            <i class="fa-solid fa-lock me-1"></i> <span x-text="isQuotaFull(track) ? 'Kuota Habis' : 'Terkunci'"></span>
          </button>
        </div>
      </div>

    </div>
  </div>
</div>
