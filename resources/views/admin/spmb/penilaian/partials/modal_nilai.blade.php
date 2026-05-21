<!-- Modal Input / Lihat Nilai per Komponen (Inline Edit) -->
<div class="modal fade" id="nilaiModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="nilaiModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title text-white">
                    <i class="ti ti-clipboard-text me-2"></i>Input Nilai:
                    <span x-text="nilaiModal.applicant_name" class="fw-bold"></span>
                    <small class="ms-2 opacity-75 font-monospace" x-text="nilaiModal.enrollment_number"></small>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <!-- Summary Bar -->
                <div class="px-4 py-3 bg-light border-bottom">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Status Saat Ini</small>
                            <span class="badge" :class="nilaiModal.status_badge" x-text="nilaiModal.status_label"></span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Jalur / Tahun Pelajaran</small>
                            <span class="fw-bold" x-text="nilaiModal.track_name"></span>
                            <small class="text-muted" x-text="'(' + nilaiModal.academic_year + ')'"></small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Skor Tertimbang Sementara</small>
                            <span class="fw-bold fs-5 text-primary" x-text="nilaiModal.weighted_score + ' / 100'"></span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Progress Komponen</small>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar"
                                        :class="nilaiModal.all_assessed ? 'bg-success' : 'bg-primary'"
                                        :style="'width: ' + nilaiModal.progress_pct + '%'">
                                    </div>
                                </div>
                                <small class="text-nowrap fw-bold"
                                    :class="nilaiModal.all_assessed ? 'text-success' : 'text-primary'"
                                    x-text="nilaiModal.assessed_count + '/' + nilaiModal.total_count">
                                </small>
                            </div>
                            <small x-show="nilaiModal.all_assessed" class="text-success fw-bold">
                                <i class="ti ti-circle-check-filled me-1"></i>Semua komponen sudah dinilai
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Table Komponen -->
                <div class="p-4">
                    <div x-show="nilaiModal.components.length === 0" class="text-center py-5">
                        <i class="ti ti-clipboard-off fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Tidak ada komponen penilaian yang dikonfigurasi untuk jalur ini.</p>
                    </div>

                    <template x-if="nilaiModal.components.length > 0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Komponen Tes</th>
                                        <th width="90" class="text-center">Bobot (%)</th>
                                        <th width="120" class="text-center">Nilai (0–100)</th>
                                        <th width="110" class="text-center">Grade</th>
                                        <th width="200">Catatan</th>
                                        <th width="120" class="text-center">Dinilai Pada</th>
                                        <th width="90" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(comp, idx) in nilaiModal.components" :key="comp.mapping_id">
                                        <tr :class="{
                                            'table-success': comp.type === 'pass_fail' ? comp.grade === 'pass' : (comp.score !== null && (!comp.passing_score || comp.score >= comp.passing_score)),
                                            'table-warning': (comp.type === 'pass_fail' ? comp.grade === null : comp.score === null) && nilaiModal.showWarning,
                                            'table-danger': comp.type === 'pass_fail' ? comp.grade === 'fail' : (comp.score !== null && comp.passing_score > 0 && comp.score < comp.passing_score)
                                        }">
                                            <td class="text-center text-muted" x-text="idx + 1"></td>
                                            <td>
                                                <div class="fw-bold" x-text="comp.assessment_name"></div>
                                                <small class="text-muted" x-show="comp.passing_score > 0">
                                                    Passing score: <span x-text="comp.passing_score"></span>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light-primary text-primary fw-bold" x-text="comp.weight + '%'"></span>
                                            </td>
                                            <td class="text-center">
                                                <template x-if="comp.type === 'score'">
                                                    <input type="number"
                                                        class="form-control form-control-sm text-center"
                                                        :id="'score_' + comp.mapping_id"
                                                        x-model="comp.score"
                                                        min="0" max="100" step="0.01"
                                                        placeholder="0–100">
                                                </template>
                                                <template x-if="comp.type !== 'score'">
                                                    <span class="text-muted font-monospace">-</span>
                                                </template>
                                            </td>
                                            <td class="text-center">
                                                <template x-if="comp.type === 'pass_fail'">
                                                    <select class="form-select form-select-sm"
                                                        :id="'grade_' + comp.mapping_id"
                                                        x-model="comp.grade">
                                                        <option value="">-- Grade --</option>
                                                        <option value="pass">Pass</option>
                                                        <option value="fail">Fail</option>
                                                    </select>
                                                </template>
                                                <template x-if="comp.type !== 'pass_fail'">
                                                    <div>
                                                        <template x-if="comp.score !== null">
                                                            <span class="badge" 
                                                                :class="(!comp.passing_score || comp.score >= comp.passing_score) ? 'bg-light-success text-success' : 'bg-light-danger text-danger'" 
                                                                x-text="(!comp.passing_score || comp.score >= comp.passing_score) ? 'Pass' : 'Fail'">
                                                            </span>
                                                        </template>
                                                        <template x-if="comp.score === null">
                                                            <span class="text-muted font-monospace">-</span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm"
                                                    :id="'notes_' + comp.mapping_id"
                                                    x-model="comp.notes"
                                                    placeholder="Catatan singkat (opsional)">
                                            </td>
                                            <td class="text-center">
                                                <template x-if="comp.assessed_at">
                                                    <div>
                                                        <small class="d-block text-success fw-bold" x-text="comp.assessed_at"></small>
                                                        <small class="text-muted" x-text="comp.assessor_name || 'Admin'"></small>
                                                    </div>
                                                </template>
                                                <template x-if="!comp.assessed_at">
                                                    <span class="text-muted fst-italic small">Belum dinilai</span>
                                                </template>
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    @click="saveScore(comp)"
                                                    :disabled="loading">
                                                    <i class="ti ti-device-floppy"></i>
                                                    <span x-show="!loading"> Simpan</span>
                                                    <span x-show="loading">...</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-muted">Total Skor Tertimbang:</td>
                                        <td colspan="5" class="fw-bold text-primary fs-5" x-text="nilaiModal.weighted_score + ' / 100'"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </template>

                    <!-- Legend -->
                    <div class="mt-3 d-flex gap-3 flex-wrap">
                        <small><span class="badge bg-success me-1">&nbsp;</span> Sudah dinilai</small>
                        <small><span class="badge bg-warning me-1">&nbsp;</span> Belum dinilai</small>
                        <small><span class="badge bg-danger me-1">&nbsp;</span> Di bawah passing score</small>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light d-flex justify-content-between">
                <div>
                    <small class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>Klik tombol <strong>Simpan</strong> per baris untuk menyimpan nilai secara individual.
                    </small>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
