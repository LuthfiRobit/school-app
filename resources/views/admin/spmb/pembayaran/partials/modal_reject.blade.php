<div class="modal fade" id="rejectModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="rejectModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form @submit.prevent="submitRejection">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="ti ti-x me-2"></i>Tolak Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" x-show="rejectForm">
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-triangle me-1"></i> Bukti pembayaran yang ditolak akan dikembalikan ke siswa agar mereka mengupload ulang bukti yang valid.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nominal Klaim Siswa</label>
                        <input type="text" class="form-control bg-light font-monospace" x-model="rejectForm.claimed_formatted" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" x-model="rejectForm.rejection_reason" rows="3" placeholder="Contoh: Bukti buram, nominal transfer tidak sesuai mutasi bank, dll." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger" :disabled="loading">
                        <span x-show="!loading"><i class="ti ti-x me-1"></i>Tolak Bukti</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
