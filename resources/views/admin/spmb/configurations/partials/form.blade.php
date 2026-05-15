<div class="col-xl-4 col-lg-5">
    <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary">
                <i class="ti ti-edit-circle me-2"></i>
                <span>Setup Konfigurasi</span>
            </h5>
            <p class="text-muted small mb-0 mt-1">Pilih tahun ajaran di tabel untuk mulai mengatur pendaftaran.</p>
        </div>
        <div class="card-body">
            <div x-show="!editId" class="text-center py-5">
                <i class="ti ti-click fs-1 text-muted mb-3 d-block"></i>
                <p class="text-muted">Silakan klik tombol <strong>Setup</strong> pada tabel untuk mengisi konfigurasi.</p>
            </div>

            <form @submit.prevent="submitForm" x-show="editId" x-transition>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Tahun Ajaran</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ti ti-calendar-event text-muted"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-start-0 fw-bold" x-model="formData.academic_year_name" readonly disabled>
                    </div>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">Tanggal Mulai SPMB <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" x-model="formData.reg_start_date" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">Tanggal Selesai SPMB <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" x-model="formData.reg_end_date" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Total Kuota Penerimaan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ti ti-users text-muted"></i>
                        </span>
                        <input type="number" class="form-control border-start-0" x-model="formData.total_quota" placeholder="Contool: 150" min="1" required>
                        <span class="input-group-text bg-light border-start-0 text-muted small">Siswa</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Status SPMB <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="ti ti-flag text-muted"></i>
                        </span>
                        <select class="form-select border-start-0" x-model="formData.status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="draft">DRAFT (Persiapan)</option>
                            <option value="active">ACTIVE (Dibuka)</option>
                            <option value="closed">CLOSED (Ditutup)</option>
                            <option value="archived">ARCHIVED (Arsip)</option>
                        </select>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm" :disabled="loading">
                        <template x-if="loading">
                            <span><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...</span>
                        </template>
                        <template x-if="!loading">
                            <span><i class="ti ti-device-floppy me-1"></i> Simpan Konfigurasi</span>
                        </template>
                    </button>
                    <button type="button" class="btn btn-light-secondary border" @click="resetForm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
