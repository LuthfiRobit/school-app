<div class="col-xl-8 col-lg-7">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-list me-2"></i>Daftar Konfigurasi SPMB</h5>
            <button class="btn btn-sm btn-light-primary" @click="refreshTable">
                <i class="ti ti-refresh me-1"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 w-100" id="configTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Aksi</th>
                            <th>Tahun Ajaran</th>
                            <th>Periode KBM</th>
                            <th>Periode SPMB</th>
                            <th>Total Kuota</th>
                            <th>Status SPMB</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data diisi via DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>