<div class="modal fade" id="detailModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="detailModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" x-show="detailData">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title text-white"><i class="ti ti-receipt me-2"></i>Detail Transaksi Masuk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Left: Proof Preview -->
                    <div class="col-lg-6 border-end">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary"><i class="ti ti-image me-1"></i>Bukti Pembayaran</h6>
                        <template x-if="detailData?.payment_proof_path">
                            <div class="bg-light p-2 rounded border text-center">
                                <template x-if="detailData.proof_url.toLowerCase().endsWith('.pdf')">
                                    <iframe :src="detailData.proof_url" class="w-100 border-0 rounded" style="height: 420px;"></iframe>
                                </template>
                                <template x-if="!detailData.proof_url.toLowerCase().endsWith('.pdf')">
                                    <a :href="detailData.proof_url" target="_blank" title="Klik untuk memperbesar">
                                        <img :src="detailData.proof_url" class="img-fluid rounded border shadow-sm mx-auto d-block" style="max-height: 420px; object-fit: contain;">
                                    </a>
                                </template>
                                <div class="mt-2">
                                    <a :href="detailData.proof_url" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-external-link me-1"></i>Buka di Tab Baru
                                    </a>
                                </div>
                            </div>
                        </template>
                        <template x-if="!detailData?.payment_proof_path">
                            <div class="text-center py-5 bg-light rounded border border-dashed">
                                <i class="ti ti-cash fs-1 text-success mb-2"></i>
                                <p class="text-muted mb-0">Pembayaran manual (cash) tidak memerlukan bukti transfer.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Right: Info & Status -->
                    <div class="col-lg-6">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary"><i class="ti ti-info-circle me-1"></i>Informasi Pendaftar & Invoice</h6>
                        <table class="table table-sm table-borderless align-middle mb-4">
                            <tr>
                                <td width="35%" class="fw-bold text-muted">Nama Pendaftar</td>
                                <td x-text="detailData?.invoice?.enrollment?.applicant?.full_name"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">No. Pendaftaran</td>
                                <td class="font-monospace fw-bold text-primary" x-text="detailData?.invoice?.enrollment?.enrollment_number || 'Draft'"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Jalur / TA</td>
                                <td>
                                    <span x-text="detailData?.invoice?.enrollment?.spmb_track?.track_type?.name"></span>
                                    (<span x-text="detailData?.invoice?.enrollment?.spmb_track?.spmb_configuration?.academic_year?.name"></span>)
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">No. Invoice</td>
                                <td class="font-monospace" x-text="detailData?.invoice?.invoice_number"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Kategori Tagihan</td>
                                <td x-text="detailData?.invoice?.category === 'registration' ? 'Formulir Pendaftaran' : 'Daftar Ulang'"></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Metode Transaksi</td>
                                <td x-html="detailData?.input_method === 'upload' ? '<span class=\'badge bg-light-info text-info\'>Upload</span>' : '<span class=\'badge bg-light-success text-success\'>Manual Cash</span>'"></td>
                            </tr>
                        </table>

                        <!-- Invoice Progress & Summary -->
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary"><i class="ti ti-calculator me-1"></i>Ringkasan Keuangan Invoice</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded text-center">
                                    <small class="text-muted d-block">Total Tagihan</small>
                                    <span class="fw-bold text-dark" x-text="detailData?.invoice_total_formatted"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded text-center">
                                    <small class="text-success d-block">Total Terbayar</small>
                                    <span class="fw-bold text-success" x-text="detailData?.invoice_paid_formatted"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded text-center">
                                    <small class="text-danger d-block">Sisa Tagihan</small>
                                    <span class="fw-bold text-danger" x-text="detailData?.invoice_unpaid_formatted"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Persentase Pelunasan</small>
                                <small class="fw-bold text-dark" x-text="getPercentPaid(detailData?.invoice) + '%'"></small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" :style="'width: ' + getPercentPaid(detailData?.invoice) + '%'"></div>
                            </div>
                        </div>

                        <!-- Transaction Specific Details -->
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary"><i class="ti ti-report-money me-1"></i>Informasi Pembayaran Ini</h6>
                        <table class="table table-sm table-bordered align-middle">
                            <tr class="table-light">
                                <td width="40%" class="fw-bold text-muted">Nominal Klaim Pendaftar</td>
                                <td class="font-monospace fw-bold fs-5 text-end text-primary" x-text="detailData?.amount_formatted"></td>
                            </tr>
                            <template x-if="detailData?.confirmed_amount !== null">
                                <tr class="table-success">
                                    <td class="fw-bold text-success">Nominal Terverifikasi</td>
                                    <td class="font-monospace fw-bold fs-5 text-end text-success" x-text="detailData?.confirmed_amount_formatted"></td>
                                </tr>
                            </template>
                            <tr>
                                <td class="fw-bold text-muted">Status Verifikasi</td>
                                <td>
                                    <span :class="'badge ' + getPaymentStatusBadge(detailData?.status)" x-text="getPaymentStatusLabel(detailData?.status)"></span>
                                </td>
                            </tr>
                            <template x-if="detailData?.status === 'rejected'">
                                <tr class="table-danger">
                                    <td class="fw-bold text-danger">Alasan Penolakan</td>
                                    <td class="text-danger fw-bold" x-text="detailData?.rejection_reason || '-'"></td>
                                </tr>
                            </template>
                            <template x-if="detailData?.confirmed_by">
                                <tr>
                                    <td class="fw-bold text-muted">Diverifikasi Oleh / Waktu</td>
                                    <td>
                                        <span x-text="detailData?.confirmed_by_user?.name || 'Admin'"></span> @ 
                                        <span x-text="detailData?.confirmed_at ? formatDateSimple(detailData.confirmed_at) : '-'"></span>
                                    </td>
                                </tr>
                            </template>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
