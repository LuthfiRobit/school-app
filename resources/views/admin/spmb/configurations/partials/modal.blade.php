<!-- Setup Modal -->
<div class="modal fade" id="setupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="setupModalLabel" x-ref="setupModal">
    <div class="modal-dialog modal-dialog-centered shadow-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title d-flex align-items-center" id="setupModalLabel">
                    <i class="ti ti-settings me-2 fs-4"></i> Setup Konfigurasi SPMB
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form @submit.prevent="submitForm">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Tahun Ajaran</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-calendar-event"></i>
                            </span>
                            <input type="text" class="form-control bg-light border-start-0 fw-bold" x-model="formData.academic_year_name" readonly>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-2">Tanggal Mulai SPMB</label>
                            <input type="date" class="form-control" x-model="formData.reg_start_date" required>
                            <div class="form-text mt-1"><i class="ti ti-info-circle me-1"></i>Pendaftaran dibuka.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-2">Tanggal Selesai SPMB</label>
                            <input type="date" class="form-control" x-model="formData.reg_end_date" required>
                            <div class="form-text mt-1"><i class="ti ti-info-circle me-1"></i>Pendaftaran ditutup.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Total Kuota Penerimaan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-users"></i>
                            </span>
                            <input type="number" class="form-control border-start-0" x-model="formData.total_quota" placeholder="Contoh: 150" min="1" required>
                            <span class="input-group-text bg-light border-start-0">Siswa</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Status SPMB</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="ti ti-flag"></i>
                            </span>
                            <select class="form-select border-start-0" x-model="formData.status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="draft">DRAFT (Persiapan)</option>
                                <option value="active">ACTIVE (Pendaftaran Dibuka)</option>
                                <option value="closed">CLOSED (Pendaftaran Ditutup)</option>
                                <option value="archived">ARCHIVED (Arsip)</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light-secondary px-4" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="loading">
                            <template x-if="loading">
                                <span><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...</span>
                            </template>
                            <template x-if="!loading">
                                <span><i class="ti ti-device-floppy me-1"></i> Simpan Konfigurasi</span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
