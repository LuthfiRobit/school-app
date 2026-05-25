<div class="card shadow-sm border-0 mb-4" x-data="uploadPaymentForm()">
  <div class="card-body p-4">
    <h5 class="fw-bold text-primary-900 mb-3"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary-600"></i> Konfirmasi Pembayaran</h5>
    
    <!-- Peringatan jika pengajuan sebelumnya ditolak -->
    @php
      $latestPayment = $payments->first();
    @endphp
    @if($latestPayment && $latestPayment->status->value === 'rejected')
      <div class="alert alert-danger border-danger border-opacity-25 p-3 mb-4 d-flex align-items-start gap-2.5">
        <i class="fa-solid fa-triangle-exclamation fs-5 text-danger mt-0.5"></i>
        <div>
          <h6 class="fw-bold text-danger mb-1">Bukti Transfer Sebelumnya Ditolak</h6>
          <p class="text-muted small mb-1">Alasan Penolakan: <strong>{{ $latestPayment->rejection_reason }}</strong></p>
          <p class="text-muted text-xxs mb-0">Silakan unggah kembali bukti pembayaran yang benar dengan nominal yang tepat.</p>
        </div>
      </div>
    @endif

    <form action="{{ route('portal.payment.upload', $enrollment->id) }}" method="POST" enctype="multipart/form-data" @submit="loading = true">
      @csrf
      
      <!-- Area Upload Bukti Pembayaran -->
      <div class="mb-4">
        <label class="form-label small fw-bold text-primary-800">Unggah Bukti Transfer <span class="text-danger">*</span></label>
        
        <div class="drag-drop-area p-4 text-center rounded-md border border-dashed transition-all duration-300 cursor-pointer"
             :class="{'border-primary bg-primary bg-opacity-5': dragOver}"
             @dragover.prevent="dragOver = true"
             @dragleave.prevent="dragOver = false"
             @drop.prevent="dragOver = false; handleFileDrop($event)"
             @click="$refs.fileInput.click()">
          
          <input type="file" name="payment_proof" x-ref="fileInput" class="d-none" accept="image/jpeg,image/png,image/jpg,application/pdf" @change="handleFileSelect">
          
          <!-- State: Kosong -->
          <div x-show="!fileSelected">
            <i class="fa-solid fa-images fs-1 text-primary-300 mb-3"></i>
            <h6 class="fw-semibold text-primary-900 small mb-1">Pilih berkas bukti pembayaran</h6>
            <p class="text-muted text-xxs mb-0">Seret berkas ke sini atau klik untuk mencari. PDF, JPG, JPEG, PNG (Maks 2MB).</p>
          </div>
          
          <!-- State: Terpilih -->
          <div x-show="fileSelected" style="display: none;">
            <div class="d-flex align-items-center justify-content-center gap-3">
              <template x-if="isImage">
                <img :src="filePreview" class="rounded border shadow-sm" style="max-height: 80px; max-width: 80px; object-fit: cover;">
              </template>
              <template x-if="!isImage">
                <div class="rounded bg-primary bg-opacity-10 text-primary-700 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                  <i class="fa-solid fa-file-pdf"></i>
                </div>
              </template>
              <div class="text-start">
                <strong class="text-primary-950 small d-block text-truncate" style="max-width: 180px;" x-text="fileName"></strong>
                <span class="text-muted text-xxs d-block" x-text="fileSize"></span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 mt-1 small"><i class="fa-solid fa-check me-1"></i> Berkas Terpilih</span>
              </div>
            </div>
          </div>
        </div>
        
        @error('payment_proof')
          <div class="text-danger small mt-1.5"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
        @enderror
      </div>
      
      <!-- Nominal Transfer Input -->
      <div class="mb-4">
        <label for="amountDisplay" class="form-label small fw-bold text-primary-800">Nominal yang Ditransfer <span class="text-danger">*</span></label>
        <div class="input-group">
          <span class="input-group-text bg-light text-primary-700 fw-semibold">Rp</span>
          <input type="text" id="amountDisplay" class="form-control fw-semibold" placeholder="0" x-model="formattedAmount" @input="formatInput">
          <input type="hidden" name="amount" x-model="rawAmount">
        </div>
        <div class="text-muted text-xxs mt-1.5"><i class="fa-solid fa-circle-info me-1"></i> Masukkan nominal persis seperti yang Anda transfer.</div>
        
        @error('amount')
          <div class="text-danger small mt-1.5"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $message }}</div>
        @enderror
      </div>
      
      <!-- Tombol Kirim -->
      <div class="d-grid">
        <button type="submit" class="btn btn-success text-white py-2.5 fw-bold" :disabled="!fileSelected || rawAmount <= 0 || loading">
          <template x-if="!loading">
            <span><i class="fa-solid fa-paper-plane me-2"></i> Kirim Bukti Transfer</span>
          </template>
          <template x-if="loading">
            <span><i class="fa-solid fa-spinner fa-spin me-2"></i> Mengunggah...</span>
          </template>
        </button>
      </div>
    </form>
  </div>
</div>
