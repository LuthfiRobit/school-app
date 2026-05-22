<div class="card h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Ringkasan per Jalur Pendaftaran</h5>
        <button type="button" class="btn btn-sm btn-light-success" @click="exportData('excel')" :disabled="loading || exportingExcel || exportingPdf">
            <template x-if="exportingExcel">
                <span><i class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></i> Mengunduh...</span>
            </template>
            <template x-if="!exportingExcel">
                <span><i class="ti ti-file-spreadsheet me-1"></i> Export Excel</span>
            </template>
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Jalur</th>
                        <th class="text-end">Kuota</th>
                        <th class="text-end">Pendaftar</th>
                        <th class="text-end">Lulus</th>
                        <th class="text-center">% Terisi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="track in tracks" :key="track.id">
                        <tr>
                            <td x-text="track.name"></td>
                            <td class="text-end" x-text="formatNumber(track.quota)"></td>
                            <td class="text-end" x-text="formatNumber(track.total_active)"></td>
                            <td class="text-end text-success fw-bold" x-text="formatNumber(track.total_passed)"></td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="badge" 
                                          :class="{
                                              'bg-danger': track.fill_percentage < 30,
                                              'bg-warning': track.fill_percentage >= 30 && track.fill_percentage < 80,
                                              'bg-success': track.fill_percentage >= 80
                                          }"
                                          x-text="track.fill_percentage + '%'"></span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="tracks.length === 0">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data untuk tahun ajaran ini.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
