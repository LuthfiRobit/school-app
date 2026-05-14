<div class="modal fade" id="detailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="ti ti-info-circle me-2"></i>Detail Jenis Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" x-show="detailData">
                <div class="mb-3 text-center">
                    <label class="small text-muted d-block mb-1">Nama Jenis Penilaian</label>
                    <h4 class="fw-bold text-primary" x-text="detailData?.name"></h4>
                    <div class="badge" :class="detailData?.is_active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary'" 
                         x-text="detailData?.is_active ? 'Status: Aktif' : 'Status: Non-Aktif'"></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150" class="fw-bold text-muted">Format Input</td>
                            <td>: 
                                <span class="badge" :class="detailData?.input_type === 'score' ? 'bg-light-info text-info' : 'bg-light-warning text-warning'"
                                      x-text="detailData?.input_type === 'score' ? 'Angka (Score)' : 'Lulus/Gagal'"></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Deskripsi</td>
                            <td>: <span x-text="detailData?.description || '-'"></span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Dibuat Oleh</td>
                            <td>: <span x-text="detailData?.creator?.name || 'System'"></span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Waktu Buat</td>
                            <td>: <span x-text="formatDate(detailData?.created_at)"></span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Terakhir Diubah</td>
                            <td>: <span x-text="formatDate(detailData?.updated_at)"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
