<div class="col-12">
    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary"><i class="ti ti-filter me-2"></i>Filter Pembayaran</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Status Pembayaran</label>
                    <select class="form-select select2-alpine" 
                        x-init="initSelect2($el, 'status')"
                        data-placeholder="Semua Transaksi">
                        <option value=""></option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Metode Input</label>
                    <select class="form-select select2-alpine" 
                        x-init="initSelect2($el, 'input_method')"
                        data-placeholder="Semua Metode">
                        <option value=""></option>
                        <option value="upload">Upload Bukti</option>
                        <option value="manual">Manual Cash</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-receipt me-2"></i>Daftar Riwayat & Verifikasi Pembayaran</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-success" @click="openManualPaymentModal">
                    <i class="ti ti-cash me-1"></i> Input Bayar Tunai (Cash)
                </button>
                <button class="btn btn-sm btn-light-primary" @click="refreshTable">
                    <i class="ti ti-refresh me-1"></i> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table id="paymentTable" class="table table-hover w-100 align-middle">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th width="50">No</th>
                            <th width="80">Aksi</th>
                            <th>No. Pendaftaran</th>
                            <th>Nama Pendaftar</th>
                            <th>No. Invoice</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-end">Terverifikasi</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
