<div class="col-xl-4 col-lg-5">
    <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="ti ti-edit-circle me-2 text-primary"></i>
                <span x-text="isEdit ? 'Edit Komponen Biaya' : 'Tambah Komponen Biaya'"></span>
            </h5>
        </div>
        <div class="card-body">
            <form @submit.prevent="submitForm">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Komponen Biaya <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" x-model="formData.name" placeholder="Misal: Uang Pangkal" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Kategori Biaya <span class="text-danger">*</span></label>
                    <select class="form-select" x-model="formData.category" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="registration">Pendaftaran (Registration)</option>
                        <option value="re_registration">Daftar Ulang (Re-registration)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Keterangan</label>
                    <textarea class="form-control" x-model="formData.description" rows="3" placeholder="Tambahkan keterangan jika perlu..."></textarea>
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" x-model="formData.is_active">
                        <label class="form-check-label fw-bold" for="is_active">Status Aktif</label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-show="!loading" x-text="isEdit ? 'Perbarui Data' : 'Simpan Data'"></span>
                        <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                    <button type="button" class="btn btn-light-secondary" @click="resetForm" x-show="isEdit">Batal Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>
