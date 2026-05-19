<!-- Clone Modal -->
<div class="modal fade" id="cloneModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cloneModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="cloneModalLabel"><i class="ti ti-copy me-2"></i>Clone Konfigurasi Jalur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="submitClone">
                <div class="modal-body p-4">
                    <div class="alert alert-warning mb-4">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Fitur ini akan menyalin seluruh data Jalur, Biaya, dan Tes dari Tahun Ajaran Sumber ke Tahun Ajaran Target. <strong>Data pada Target yang sudah ada mungkin akan ditimpa.</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Tahun Ajaran Sumber (Asal Data)</label>
                        <select class="form-select select2-clone-source" x-model="cloneData.source_configuration_id" required>
                            <option value="">-- Pilih Sumber --</option>
                            <template x-for="config in configurationsWithTracks" :key="config.id">
                                <option :value="config.id" x-text="config.academic_year.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label required">Tahun Ajaran Target (Tujuan Salin)</label>
                        <select class="form-select select2-clone-target" x-model="cloneData.target_configuration_id" required>
                            <option value="">-- Pilih Target --</option>
                            <template x-for="config in configurationsWithoutTracks" :key="config.id">
                                <option :value="config.id" x-text="config.academic_year.name" :disabled="config.id == cloneData.source_configuration_id"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4" :disabled="loading">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" x-show="loading"></span>
                        <i class="ti ti-copy me-1" x-show="!loading"></i>
                        Proses Clone
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
