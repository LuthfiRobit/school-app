<div class="modal fade" id="modalFinalisasi" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title text-white">
                    <i class="ti ti-circle-check me-2"></i>Konfirmasi Finalisasi Daftar Ulang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-alert-triangle text-warning display-4 mb-3 d-block"></i>
                <h5 class="mb-2 fw-bold">Anda yakin ingin memfinalisasi pendaftar ini?</h5>
                <p class="text-primary fw-bold fs-5 mb-3" x-text="selectedApplicantName || '-'"></p>
                <p class="text-muted mb-0">Pendaftar akan diubah statusnya menjadi <strong>Siswa Tetap</strong> dan Surat Pernyataan akan dapat diunduh.</p>
                <div class="alert alert-info mt-3 text-start mb-0">
                    <div class="d-flex">
                        <i class="ti ti-info-circle fs-4 me-2"></i>
                        <div>
                            <strong class="d-block mb-1">Informasi Penting:</strong>
                            <ul class="mb-0 ps-3">
                                <li>Pastikan seluruh tagihan daftar ulang telah lunas.</li>
                                <li>Aksi ini bersifat final dan tidak dapat dibatalkan.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" :disabled="loading">Batal</button>
                <button type="button" class="btn btn-success" @click="submitFinalisasi" :disabled="loading">
                    <span x-show="!loading"><i class="ti ti-checkbox me-1"></i> Ya, Finalisasi</span>
                    <span x-show="loading"><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>
