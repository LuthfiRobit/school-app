<div class="col-xl-8 col-lg-7">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-list me-2"></i>Daftar Komponen Biaya</h5>
            <div class="d-flex gap-2">
                <div x-show="selectedIds.length > 0" x-transition x-cloak>
                    <div class="d-flex gap-2 me-2 border-end pe-3">
                        <button class="btn btn-sm btn-success" @click="bulkStatus(true)">
                            <i class="ti ti-check me-1"></i> Aktifkan (<span x-text="selectedIds.length"></span>)
                        </button>
                        <button class="btn btn-sm btn-danger" @click="bulkStatus(false)">
                            <i class="ti ti-x me-1"></i> Non-Aktifkan
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
                <table id="feeTable" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th width="30">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" @change="toggleAll($event)">
                                </div>
                            </th>
                            <th width="50">No</th>
                            <th width="80">Aksi</th>
                            <th>Nama Komponen</th>
                            <th>Kategori</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
