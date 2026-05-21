<div class="col-12">
    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary"><i class="ti ti-filter me-2"></i>Filter Penilaian</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jalur Pendaftaran</label>
                    <select class="form-select select2-alpine"
                        x-init="initSelect2($el, 'spmb_track_id')"
                        data-placeholder="Semua Jalur">
                        <option value=""></option>
                        @foreach($tracks as $track)
                            <option value="{{ $track->id }}">{{ $track->trackType->name }} ({{ $track->spmbConfiguration->academicYear->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Status Pendaftar</label>
                    <select class="form-select select2-alpine"
                        x-init="initSelect2($el, 'status')"
                        data-placeholder="Semua Status Penilaian">
                        <option value=""></option>
                        @foreach($statuses as $status)
                            <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-clipboard-list me-2"></i>Daftar Pendaftar untuk Dinilai</h5>
            <div class="d-flex gap-2">
                <!-- Bulk Action: tampil hanya jika ada checkbox terpilih -->
                <div x-show="selectedIds.length > 0" x-transition x-cloak>
                    <div class="d-flex gap-2 me-2 border-end pe-3">
                        <button class="btn btn-sm btn-warning text-dark" @click="openBulkDecisionModal">
                            <i class="ti ti-award me-1"></i> Tetapkan Kelulusan Massal (<span x-text="selectedIds.length"></span>)
                        </button>
                    </div>
                </div>
                <button class="btn btn-sm btn-light-primary" @click="refreshTable">
                    <i class="ti ti-refresh me-1"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="assessmentTable" class="table table-hover w-100 align-middle">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th width="30">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" @change="toggleAll($event)">
                                </div>
                            </th>
                            <th width="50">No</th>
                            <th width="80">Aksi</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama Lengkap</th>
                            <th>Jalur Pendaftaran</th>
                            <th>Tahun Pelajaran</th>
                            <th>Progress Penilaian</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
