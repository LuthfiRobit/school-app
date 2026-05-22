<div class="card mb-4 border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary"><i class="ti ti-filter me-2"></i>Filter Laporan & Export</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tahun Ajaran</label>
                <select class="form-select select2-alpine"
                    x-init="initSelect2($el, 'academic_year_id')"
                    data-placeholder="-- Pilih Tahun Ajaran --">
                    <option value=""></option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Jalur Pendaftaran (Untuk Export)</label>
                <select class="form-select select2-alpine"
                    x-init="initSelect2($el, 'spmb_track_id')"
                    data-placeholder="-- Semua Jalur --">
                    <option value=""></option>
                    @foreach($tracks as $track)
                        <option value="{{ $track->id }}">{{ $track->trackType?->name ?? '-' }} ({{ $track->spmbConfiguration?->academicYear?->name ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Status (Untuk Export)</label>
                <select class="form-select select2-alpine"
                    x-init="initSelect2($el, 'status')"
                    data-placeholder="-- Semua Status --">
                    <option value=""></option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 text-end mt-4">
                <div class="btn-group" role="group">
                    <button type="button"
                        class="btn btn-outline-secondary"
                        @click="exportData('pdf')"
                        :disabled="loading || exportingPdf || exportingExcel">
                        <template x-if="exportingPdf">
                            <span><i class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></i>Mengunduh...</span>
                        </template>
                        <template x-if="!exportingPdf">
                            <span><i class="ti ti-file-pdf me-2 text-danger"></i>Download PDF Summary</span>
                        </template>
                    </button>
                    <button type="button"
                        class="btn btn-outline-success"
                        @click="exportData('excel')"
                        :disabled="loading || exportingExcel || exportingPdf">
                        <template x-if="exportingExcel">
                            <span><i class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></i>Mengunduh...</span>
                        </template>
                        <template x-if="!exportingExcel">
                            <span><i class="ti ti-file-spreadsheet me-2"></i>Download Data Excel</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
