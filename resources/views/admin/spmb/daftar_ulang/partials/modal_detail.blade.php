<div class="modal fade" id="modalDetail" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" x-show="detailData">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="ti ti-receipt me-2"></i>Detail Pembayaran Daftar Ulang: <span x-text="detailData.applicant ? detailData.applicant.full_name : '-'"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border-start border-primary border-3 h-100">
                            <span class="text-muted small fw-bold d-block">Nama Lengkap</span>
                            <span class="fw-bold text-dark fs-5 mt-1 d-block" x-text="detailData.applicant ? detailData.applicant.full_name : '-'"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border-start border-info border-3 h-100">
                            <span class="text-muted small fw-bold d-block">Jalur / TA</span>
                            <span class="fw-bold text-dark fs-5 mt-1 d-block">
                                <span x-text="detailData.spmb_track && detailData.spmb_track.track_type ? detailData.spmb_track.track_type.name : '-'"></span>
                                (<span x-text="detailData.spmb_track && detailData.spmb_track.spmb_configuration && detailData.spmb_track.spmb_configuration.academic_year ? detailData.spmb_track.spmb_configuration.academic_year.name : '-'"></span>)
                            </span>
                        </div>
                    </div>
                </div>

                <div x-show="detailData.invoices && detailData.invoices.length > 0">
                    <template x-for="invoice in detailData.invoices" :key="invoice.id">
                        <div class="card border border-light-subtle shadow-none mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <span class="fw-bold text-primary font-monospace" x-text="invoice.invoice_number"></span>
                                    <span class="badge bg-light-primary text-primary ms-2">Biaya Daftar Ulang</span>
                                </div>
                                <div>
                                    <span class="fw-bold me-2">Status:</span>
                                    <span :class="'badge ' + getInvoiceStatusBadge(invoice.status)" x-text="getInvoiceStatusLabel(invoice.status)"></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Total Tagihan</small>
                                        <span class="fw-bold fs-5 text-dark font-monospace" x-text="formatRupiah(invoice.total_amount)"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Sudah Dibayar</small>
                                        <span class="fw-bold fs-5 text-success font-monospace" x-text="formatRupiah(invoice.paid_amount)"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Sisa Tagihan</small>
                                        <span class="fw-bold fs-5 text-danger font-monospace" x-text="formatRupiah(Math.max(0, invoice.total_amount - invoice.paid_amount))"></span>
                                    </div>
                                </div>

                                <div class="progress mb-4" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                        :style="`width: ${Math.min(100, (invoice.paid_amount / invoice.total_amount) * 100)}%`" 
                                        :aria-valuenow="(invoice.paid_amount / invoice.total_amount) * 100" 
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>

                                <h6 class="fw-bold text-secondary mb-2"><i class="ti ti-history me-1"></i>Riwayat Pembayaran Dikonfirmasi</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-bordered align-middle m-0">
                                        <thead class="bg-light text-muted">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Metode</th>
                                                <th class="text-end">Nominal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-if="invoice.payments && invoice.payments.length > 0">
                                                <template x-for="payment in invoice.payments" :key="payment.id">
                                                    <tr>
                                                        <td x-text="formatDate(payment.confirmed_at || payment.created_at)"></td>
                                                        <td>
                                                            <span x-html="payment.input_method === 'upload' ? '<i class=\'ti ti-upload text-info me-1\'></i>Upload' : '<i class=\'ti ti-cash text-success me-1\'></i>Tunai'"></span>
                                                        </td>
                                                        <td class="text-end font-monospace" x-text="formatRupiah(payment.confirmed_amount || payment.amount)"></td>
                                                        <td>
                                                            <span class="badge bg-light-success text-success">Dikonfirmasi</span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <template x-if="!invoice.payments || invoice.payments.length === 0">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">Belum ada pembayaran dikonfirmasi</td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="!detailData.invoices || detailData.invoices.length === 0" class="text-center p-4">
                    <i class="ti ti-receipt-off fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada tagihan daftar ulang.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
