<!-- Modal Validasi Formulir -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-hidden="true" x-ref="validationModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="ti ti-check me-2"></i>Validasi Formulir & Berkas Pendaftaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div class="d-flex align-items-center mb-4 p-3 bg-primary-subtle rounded border border-primary border-opacity-25">
                    <div class="me-3">
                        <i class="ti ti-user fs-1 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-primary" x-text="validationData?.enrollment?.applicant?.full_name || '-'"></h6>
                        <span class="text-muted small" x-text="'No. Reg: ' + (validationData?.enrollment?.enrollment_number || 'Belum Terdaftar')"></span>
                    </div>
                </div>

                <div class="alert alert-info border-info border-opacity-25 mb-4">
                    <i class="ti ti-info-circle me-2"></i>
                    Silakan periksa setiap isian dan berkas pendaftar. Tandai sebagai <strong>Valid</strong> atau <strong>Tidak Valid</strong>. Jika ada yang tidak valid, berikan catatan revisi agar pendaftar dapat memperbaikinya. Pendaftar dengan dokumen tidak valid akan otomatis dikembalikan ke status <strong>Draft</strong>.
                </div>

                <template x-if="validationData?.enrollment?.form_data && validationData.enrollment.form_data.length > 0">
                    <div class="row g-3">
                        <template x-for="(item, index) in validationData.enrollment.form_data" :key="item.id">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded shadow-xs border-start border-3 h-100"
                                    :class="{
                                        'border-primary': item.is_valid === null || item.is_valid === 'null',
                                        'border-success bg-success-subtle': String(item.is_valid) === 'true',
                                        'border-danger bg-danger-subtle': String(item.is_valid) === 'false'
                                    }">
                                    
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="d-block text-muted small fw-bold" x-text="item.field?.field_label"></span>
                                        
                                        <!-- Dropdown Validasi -->
                                        <select class="form-select form-select-sm w-auto shadow-sm" x-model="item.is_valid">
                                            <option :value="null">Belum Diperiksa</option>
                                            <option :value="true">Valid</option>
                                            <option :value="false">Tidak Valid</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <template x-if="item.field?.field_type === 'file'">
                                            <a :href="'/' + item.value" target="_blank" class="btn btn-sm btn-outline-primary w-100" x-show="item.value">
                                                <i class="ti ti-external-link"></i> Lihat Berkas
                                            </a>
                                        </template>
                                        <template x-if="item.field?.field_type !== 'file'">
                                            <span class="d-block fw-bold text-dark border p-2 bg-white rounded" x-text="item.value || '-'"></span>
                                        </template>
                                    </div>

                                    <!-- Catatan Validasi -->
                                    <div x-show="String(item.is_valid) === 'false'" class="mt-2 pt-2 border-top border-danger border-opacity-25" x-transition>
                                        <label class="small text-danger fw-bold mb-1"><i class="ti ti-alert-circle"></i> Catatan Revisi:</label>
                                        <textarea class="form-control form-control-sm border-danger" rows="2" 
                                            x-model="item.validation_note" 
                                            placeholder="Contoh: KTP buram, silakan upload ulang..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!validationData?.enrollment?.form_data || validationData.enrollment.form_data.length === 0">
                    <div class="text-center py-4">
                        <i class="ti ti-file-off fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">Belum ada data tambahan yang diisi oleh pendaftar.</p>
                    </div>
                </template>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" @click="submitValidation" :disabled="loading || (!validationData?.enrollment?.form_data || validationData.enrollment.form_data.length === 0)">
                    <span x-show="!loading"><i class="ti ti-device-floppy me-2"></i>Simpan Validasi</span>
                    <span x-show="loading"><i class="ti ti-loader fa-spin me-2"></i>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
