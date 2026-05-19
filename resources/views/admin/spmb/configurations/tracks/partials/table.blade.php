<div class="col-lg-8">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="ti ti-list text-primary me-2"></i> Daftar Jalur Terdaftar
            </h5>
            <button type="button" class="btn btn-light-primary btn-sm rounded-pill" @click="refreshTable">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="trackTable" class="table table-hover table-striped align-middle border-bottom w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center" width="15%">Aksi</th>
                            <th width="20%">Jalur</th>
                            <th width="15%">Kuota</th>
                            <th width="20%">Biaya Pendaftaran</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>