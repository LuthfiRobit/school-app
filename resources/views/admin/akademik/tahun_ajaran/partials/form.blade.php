<!-- Kolom Kiri: Form Input / Edit -->
<div class="col-xl-4 col-lg-5">
    <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="ti ti-edit-circle me-2 text-primary"></i>
                <span x-text="editMode ? 'Edit Tahun Pelajaran' : 'Tambah Tahun Pelajaran'"></span>
            </h5>
        </div>
        <div class="card-body">
            <form @submit.prevent="saveData()">
                <!-- Identitas Utama -->
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Nama Tahun Pelajaran</label>
                    <input type="text" class="form-control" x-model="formData.name" placeholder="Contoh: 2024/2025" required>
                    <small class="text-muted">Format: YYYY/YYYY (Sesuai SK)</small>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Mulai TP</label>
                            <input type="date" class="form-control" x-model="formData.start_date" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Selesai TP</label>
                            <input type="date" class="form-control" x-model="formData.end_date" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4 opacity-50">

                <!-- Semester Ganjil -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold text-primary"><i class="ti ti-circle-number-1 me-1"></i> Semester Ganjil</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="semester_active" value="ganjil" x-model="formData.semester_active">
                            <label class="form-check-label small">Aktif</label>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" class="form-control form-control-sm" x-model="formData.ganjil_start_date" title="Tanggal Mulai Ganjil">
                        </div>
                        <div class="col-6">
                            <input type="date" class="form-control form-control-sm" x-model="formData.ganjil_end_date" title="Tanggal Selesai Ganjil">
                        </div>
                    </div>
                </div>

                <!-- Semester Genap -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold text-success"><i class="ti ti-circle-number-2 me-1"></i> Semester Genap</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="semester_active" value="genap" x-model="formData.semester_active">
                            <label class="form-check-label small">Aktif</label>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" class="form-control form-control-sm" x-model="formData.genap_start_date" title="Tanggal Mulai Genap">
                        </div>
                        <div class="col-6">
                            <input type="date" class="form-control form-control-sm" x-model="formData.genap_end_date" title="Tanggal Selesai Genap">
                        </div>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="is_active" x-model="formData.is_active">
                    <label class="form-check-label fw-bold" for="is_active">Jadikan Tahun Pelajaran Aktif</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-show="!loading" x-text="editMode ? 'Perbarui Data' : 'Simpan Data'"></span>
                        <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                    </button>
                    <button type="button" class="btn btn-light-secondary" x-show="editMode" @click="resetForm()">Batal Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>
