<!-- Kolom Kanan: Tabel Riwayat -->
<div class="col-xl-8 col-lg-7">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-list me-2"></i>Daftar Tahun Pelajaran</h5>
            <button class="btn btn-sm btn-light-primary" @click="refreshTable()">
                <i class="ti ti-refresh me-1"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="academic-year-table" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Aksi</th>
                            <th>Tahun Pelajaran</th>
                            <th>Periode</th>
                            <th width="10%">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
