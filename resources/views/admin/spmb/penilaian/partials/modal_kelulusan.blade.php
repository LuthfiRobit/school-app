<!-- Modal Penetapan Status Kelulusan (Individual) -->
<div class="modal fade" id="kelulusanModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="kelulusanModal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form @submit.prevent="submitDecision">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ti ti-award me-2"></i>Tetapkan Status Kelulusan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Info Pendaftar -->
                    <div class="alert alert-light border mb-4 d-flex align-items-start gap-3">
                        <i class="ti ti-user-check fs-3 text-primary mt-1"></i>
                        <div>
                            <div class="fw-bold" x-text="decisionForm.applicant_name"></div>
                            <small class="text-muted font-monospace" x-text="decisionForm.enrollment_number"></small>
                            <div class="mt-1">
                                <span class="badge" :class="decisionForm.status_badge" x-text="decisionForm.status_label"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Skor Tertimbang (informatif) -->
                    <div class="mb-4 p-3 bg-light rounded border" x-show="decisionForm.weighted_score !== null">
                        <small class="text-muted d-block">Skor Tertimbang Akhir</small>
                        <span class="fw-bold fs-4 text-primary" x-text="decisionForm.weighted_score + ' / 100'"></span>
                        <small class="text-muted ms-2" x-show="!decisionForm.all_assessed" class="text-warning">
                            <i class="ti ti-alert-triangle text-warning me-1"></i>Belum semua komponen dinilai
                        </small>
                    </div>

                    <!-- Pilih Status Kelulusan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tetapkan Status Kelulusan <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <!-- PASSED -->
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="kelulusan_status" id="kl_passed" value="passed" x-model="decisionForm.status">
                                <label class="btn btn-outline-success w-100 py-3" for="kl_passed">
                                    <i class="ti ti-trophy d-block fs-2 mb-1"></i>
                                    <span class="fw-bold">Lulus</span>
                                </label>
                            </div>
                            <!-- WAITING LIST -->
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="kelulusan_status" id="kl_waiting" value="waiting_list" x-model="decisionForm.status" @change="if(!decisionForm.waitlist_order) decisionForm.waitlist_order = decisionForm.waitlist_info.next_suggested">
                                <label class="btn btn-outline-warning w-100 py-3" for="kl_waiting">
                                    <i class="ti ti-clock d-block fs-2 mb-1"></i>
                                    <span class="fw-bold">Cadangan</span>
                                </label>
                            </div>
                            <!-- REJECTED -->
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="kelulusan_status" id="kl_rejected" value="rejected" x-model="decisionForm.status">
                                <label class="btn btn-outline-danger w-100 py-3" for="kl_rejected">
                                    <i class="ti ti-x d-block fs-2 mb-1"></i>
                                    <span class="fw-bold">Tidak Lulus</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Field Nomor Urut Cadangan (muncul jika WAITING_LIST dipilih) -->
                    <div class="mb-3" x-show="decisionForm.status === 'waiting_list'" x-transition>
                        <label class="form-label fw-bold">Nomor Urut Cadangan <span class="text-danger">*</span></label>
                        <input type="number"
                            class="form-control"
                            x-model="decisionForm.waitlist_order"
                            :required="decisionForm.status === 'waiting_list'"
                            min="1"
                            placeholder="Contoh: 1, 2, 3, ...">
                        <small class="text-muted">Urutan cadangan menentukan prioritas jika ada slot terbuka.</small>

                        <!-- Waitlist Info / Priority Statistics -->
                        <div class="mt-3 p-3 rounded-3" style="background-color: #fff9eb; border: 1px solid #ffe8a1; color: #664d03;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-info-circle fs-4 text-warning"></i>
                                <span class="fw-bold text-dark">Informasi Antrean Cadangan Jalur Ini</span>
                            </div>
                            <div class="row g-2 mb-2 text-dark small">
                                <div class="col-6">
                                    <span class="text-muted">Total Cadangan:</span>
                                    <strong x-text="decisionForm.waitlist_info.current_count"></strong> orang
                                </div>
                                <div class="col-6">
                                    <span class="text-muted">Urutan Terakhir:</span>
                                    <strong x-text="decisionForm.waitlist_info.max_order || '-'"></strong>
                                </div>
                            </div>
                            
                            <!-- Suggested action if not matching recommended -->
                            <div class="small mb-2" x-show="decisionForm.waitlist_info.next_suggested !== parseInt(decisionForm.waitlist_order)">
                                <span class="badge bg-warning text-dark">Rekomendasi</span>
                                <span class="text-muted">Gunakan urutan ke-</span>
                                <strong class="text-dark" x-text="decisionForm.waitlist_info.next_suggested"></strong>
                                <a href="javascript:void(0)" class="text-primary fw-bold text-decoration-none ms-1" @click="decisionForm.waitlist_order = decisionForm.waitlist_info.next_suggested">Terapkan</a>
                            </div>

                            <!-- List of current waitlisted students (Collapsible) -->
                            <div x-data="{ showList: false }" class="mt-2 pt-2 border-top" style="border-top-color: #ffe8a1 !important;">
                                <a href="javascript:void(0)" class="d-flex justify-content-between align-items-center text-decoration-none text-muted small" @click="showList = !showList">
                                    <span>Lihat Daftar Antrean Cadangan</span>
                                    <i class="ti fs-6" :class="showList ? 'ti-chevron-up' : 'ti-chevron-down'"></i>
                                </a>
                                <div class="mt-2" x-show="showList" x-transition>
                                    <template x-if="decisionForm.waitlist_info.list.length === 0">
                                        <div class="text-muted small fst-italic">Belum ada pendaftar di antrean cadangan.</div>
                                    </template>
                                    <template x-if="decisionForm.waitlist_info.list.length > 0">
                                        <div class="list-group list-group-flush border rounded-3 bg-white" style="max-height: 150px; overflow-y: auto;">
                                            <template x-for="item in decisionForm.waitlist_info.list" :key="item.id">
                                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 small"
                                                     :class="item.id === decisionForm.enrollment_id ? 'bg-light-primary fw-bold' : ''">
                                                    <div>
                                                        <span x-text="item.applicant_name"></span>
                                                        <span class="text-muted font-monospace ms-1 small" x-text="'(' + item.enrollment_number + ')'"></span>
                                                        <template x-if="item.id === decisionForm.enrollment_id">
                                                            <span class="badge bg-primary ms-1">Pendaftar Ini</span>
                                                        </template>
                                                    </div>
                                                    <span class="badge bg-warning text-dark rounded-pill fw-bold" x-text="'#' + item.waitlist_order"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Field Alasan (muncul jika REJECTED dipilih) -->
                    <div class="mb-3" x-show="decisionForm.status === 'rejected'" x-transition>
                        <label class="form-label fw-bold">Alasan Tidak Lulus <span class="text-danger">*</span></label>
                        <textarea class="form-control"
                            x-model="decisionForm.reason"
                            rows="3"
                            placeholder="Contoh: Nilai rata-rata di bawah ambang batas kelulusan, dsb."></textarea>
                    </div>

                    <!-- Catatan Umum -->
                    <div class="mb-3" x-show="decisionForm.status !== '' && decisionForm.status !== 'rejected'" x-transition>
                        <label class="form-label fw-bold text-muted">Catatan (Opsional)</label>
                        <textarea class="form-control"
                            x-model="decisionForm.reason"
                            rows="2"
                            placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark"
                        :disabled="loading || !decisionForm.status">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Tetapkan Kelulusan</span>
                        <span x-show="loading"><i class="ti ti-loader me-1 spin"></i>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Penetapan Status Kelulusan MASSAL -->
<div class="modal fade" id="bulkKelulusanModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="bulkKelulusanModal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <form @submit.prevent="submitBulkDecision">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="ti ti-award me-2"></i>Tetapkan Kelulusan Massal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Summary Massal -->
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                        <i class="ti ti-users fs-3"></i>
                        <div>
                            Anda akan menetapkan status kelulusan untuk
                            <strong><span x-text="selectedIds.length"></span> pendaftar</strong> yang dipilih secara bersamaan.
                        </div>
                    </div>

                    <!-- Pilih Status Kelulusan Massal -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tetapkan Status Kelulusan untuk Semua <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <!-- PASSED -->
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="bulk_status" id="bkl_passed" value="passed" x-model="bulkDecisionForm.status">
                                <label class="btn btn-outline-success w-100 py-3" for="bkl_passed">
                                    <i class="ti ti-trophy d-block fs-2 mb-1"></i>
                                    <span class="fw-bold">Lulus</span>
                                </label>
                            </div>
                            <!-- WAITING LIST -->
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="bulk_status" id="bkl_waiting" value="waiting_list" x-model="bulkDecisionForm.status">
                                <label class="btn btn-outline-warning w-100 py-3" for="bkl_waiting">
                                    <i class="ti ti-clock d-block fs-2 mb-1"></i>
                                    <span class="fw-bold">Cadangan</span>
                                </label>
                            </div>
                            <!-- REJECTED -->
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="bulk_status" id="bkl_rejected" value="rejected" x-model="bulkDecisionForm.status">
                                <label class="btn btn-outline-danger w-100 py-3" for="bkl_rejected">
                                    <i class="ti ti-x d-block fs-2 mb-1"></i>
                                    <span class="fw-bold">Tidak Lulus</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Massal -->
                    <div class="mb-3" x-show="bulkDecisionForm.status !== ''" x-transition>
                        <label class="form-label fw-bold">Alasan / Catatan <span class="text-danger">*</span></label>
                        <textarea class="form-control"
                            x-model="bulkDecisionForm.reason"
                            rows="3"
                            placeholder="Contoh: Ditetapkan berdasarkan hasil rapat seleksi, dsb."
                            required></textarea>
                        <small class="text-muted">
                            <i class="ti ti-info-circle me-1"></i>Pendaftar yang tidak memenuhi kondisi untuk transisi akan otomatis dilewati.
                        </small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark"
                        :disabled="loading || !bulkDecisionForm.status">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Terapkan ke <span x-text="selectedIds.length"></span> Pendaftar</span>
                        <span x-show="loading"><i class="ti ti-loader me-1 spin"></i>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
