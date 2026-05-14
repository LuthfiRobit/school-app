<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="ti ti-info-circle me-2"></i>Detail Tahun Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" x-show="detailData">
                <div class="mb-3 text-center">
                    <label class="small text-muted d-block mb-1">Nama Tahun Pelajaran</label>
                    <h4 class="fw-bold text-primary" x-text="detailData?.name"></h4>
                    <div class="badge" :class="detailData?.is_active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary'" 
                         x-text="detailData?.is_active ? 'Status: Aktif' : 'Status: Non-Aktif'"></div>
                </div>
                
                <div class="row mb-4 text-center border-top border-bottom py-3 mx-0 bg-light rounded">
                    <div class="col-6 border-end">
                        <label class="small text-muted d-block">Mulai</label>
                        <span class="fw-bold" x-text="formatDate(detailData?.start_date)"></span>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted d-block">Selesai</label>
                        <span class="fw-bold" x-text="formatDate(detailData?.end_date)"></span>
                    </div>
                </div>
                
                <h6 class="fw-bold mb-3"><i class="ti ti-calendar-event me-2"></i>Rincian Semester</h6>
                <template x-for="sem in detailData?.semesters" :key="sem.id">
                    <div class="card border mb-2 shadow-none">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-capitalize text-primary" x-text="'Semester ' + sem.type"></span>
                                <span class="badge" :class="sem.is_active ? 'bg-success' : 'bg-secondary'" x-text="sem.is_active ? 'Aktif' : 'Non-Aktif'"></span>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="ti ti-calendar me-2"></i>
                                <span x-text="formatDate(sem.start_date) + ' — ' + formatDate(sem.end_date)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
