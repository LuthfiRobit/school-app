<div class="modal fade" id="confirmModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="confirmModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form @submit.prevent="submitConfirmation">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="ti ti-check me-2"></i>Konfirmasi Verifikasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" x-show="confirmForm">
                    <div class="alert alert-success">
                        <i class="ti ti-info-circle me-1"></i> Harap masukkan jumlah nominal yang masuk ke rekening sekolah berdasarkan mutasi bank.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nominal yang Diklaim Siswa</label>
                        <input type="text" class="form-control bg-light font-monospace" x-model="confirmForm.claimed_formatted" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nominal Riil yang Diterima <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">Rp</span>
                            <input type="number" step="0.01" min="0.01" class="form-control font-monospace fw-bold" x-model="confirmForm.confirmed_amount" required>
                        </div>
                        <small class="text-muted d-block mt-1">Isi nominal aktual (default: nominal klaim siswa).</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" :disabled="loading">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Verifikasi & Setujui</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
