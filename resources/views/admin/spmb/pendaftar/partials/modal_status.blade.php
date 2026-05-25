<!-- Individual Status Modal -->
<div class="modal fade" id="statusModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="statusModal">
    <div class="modal-dialog modal-dialog-centered">
        <form @submit.prevent="submitStatusChange">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="ti ti-arrows-left-right me-2"></i>Ubah Status Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control bg-light" x-model="statusForm.name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Saat Ini</label>
                        <div>
                            <span class="badge" :class="statusForm.currentBadge" x-text="statusForm.currentLabel"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Status Baru <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="statusForm.status" required>
                            <option value="">-- Pilih Status Target --</option>
                            <template x-for="opt in statusForm.allowedTransitions" :key="opt.value">
                                <option :value="opt.value" x-text="opt.label"></option>
                            </template>
                        </select>
                        <small class="text-muted d-block mt-1">Hanya status transisi yang valid yang ditampilkan sesuai State Machine.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Perubahan <span class="text-danger">*</span></label>
                        <textarea class="form-control" x-model="statusForm.reason" rows="3" placeholder="Contoh: Dokumen lengkap, lulus tes administrasi, dll." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Simpan Perubahan</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Status Modal -->
<div class="modal fade" id="bulkStatusModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="bulkStatusModal">
    <div class="modal-dialog modal-dialog-centered">
        <form @submit.prevent="submitBulkStatusChange">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="ti ti-arrows-left-right me-2"></i>Ubah Status Massal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-1"></i> Anda memilih <span class="fw-bold" x-text="selectedIds.length"></span> pendaftar untuk diubah statusnya bersamaan.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Status Target Baru <span class="text-danger">*</span></label>
                        <select class="form-select" x-model="bulkStatusForm.status" required>
                            <option value="">-- Pilih Status Target --</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Catatan: Pastikan semua pendaftar yang dipilih dapat berpindah ke status target ini.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Perubahan Massal <span class="text-danger">*</span></label>
                        <textarea class="form-control" x-model="bulkStatusForm.reason" rows="3" placeholder="Contoh: Lolos verifikasi berkas tahap 1 massal" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Terapkan Massal</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
