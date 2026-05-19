<div class="col-lg-4">
    <div class="card shadow-sm border-0 rounded-3 position-sticky" style="top: 80px;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold" x-text="editId ? 'Edit Jalur' : 'Tambah Jalur Baru'"></h5>
        </div>
        <div class="card-body p-4">
            <form @submit.prevent="submitForm" id="trackForm">
                
                <div class="mb-3">
                    <label class="form-label required">Jalur Master</label>
                    <select class="form-select select2-master" name="master_track_type_id" x-model="formData.master_track_type_id" required :disabled="editId !== null">
                        <option value="">-- Pilih Jalur --</option>
                        @foreach($masterTracks as $track)
                            <option value="{{ $track->id }}">{{ $track->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Kuota</label>
                    <div class="input-group">
                        <input type="number" class="form-control" x-model="formData.quota" min="0" required placeholder="Contoh: 100">
                        <span class="input-group-text">Siswa</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Biaya Pendaftaran</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" x-model="formData.registration_fee" min="0" required placeholder="0">
                    </div>
                    <small class="text-muted">Isi 0 jika gratis.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label required">Mode Pembayaran</label>
                    <select class="form-select" x-model="formData.payment_mode" required>
                        <option value="PRE_PAYMENT">Bayar Daftar Dulu (Pre-Payment)</option>
                        <option value="POST_PAYMENT">Bayar Nanti (Post-Payment)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="allow_carryover" x-model="formData.allow_carryover">
                        <label class="form-check-label" for="allow_carryover">Izinkan Lempar Kuota Sisa?</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Pengumuman</label>
                    <input type="datetime-local" class="form-control" x-model="formData.announcement_date">
                    <small class="text-muted">Kapan hasil seleksi dapat dilihat pendaftar.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label required">Status</label>
                    <select class="form-select" x-model="formData.status" required>
                        <option value="active">Buka (Active)</option>
                        <option value="closed">Ditutup (Closed)</option>
                        <option value="full">Kuota Penuh (Full)</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill d-flex align-items-center justify-content-center" :disabled="loading">
                        <i class="ti ti-device-floppy me-2" x-show="!loading"></i>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" x-show="loading"></span>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Jalur'"></span>
                    </button>
                    <button type="button" class="btn btn-light-secondary rounded-pill w-100" @click="resetForm" x-show="editId">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
