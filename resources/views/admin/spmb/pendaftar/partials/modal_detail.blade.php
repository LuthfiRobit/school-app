<div class="modal fade" id="detailModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" x-ref="detailModal">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" x-show="detailData">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="ti ti-user-check me-2"></i>Detail Pendaftar: <span x-text="detailData?.enrollment?.applicant?.full_name"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified border-bottom" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active py-3 fw-bold" data-bs-toggle="tab" href="#tab-profil" role="tab">
                            <i class="ti ti-id me-2"></i>Profil & Dapodik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 fw-bold" data-bs-toggle="tab" href="#tab-formulir" role="tab">
                            <i class="ti ti-file-text me-2"></i>Data Formulir
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 fw-bold" data-bs-toggle="tab" href="#tab-keuangan" role="tab">
                            <i class="ti ti-receipt me-2"></i>Invoices & Pembayaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 fw-bold" data-bs-toggle="tab" href="#tab-logs" role="tab">
                            <i class="ti ti-history me-2"></i>Log Status
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-4">
                    <!-- Tab Profil & Dapodik -->
                    <div class="tab-pane active" id="tab-profil" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary">Identitas Legal (Standar Dapodik)</h6>
                                <table class="table table-borderless align-middle m-0">
                                    <tr>
                                        <td width="35%" class="fw-bold text-muted">Nama Lengkap</td>
                                        <td x-text="detailData?.enrollment?.applicant?.full_name"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Jenis Kelamin</td>
                                        <td x-text="detailData?.enrollment?.applicant?.gender === 'L' ? 'Laki-laki' : 'Perempuan'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">NIK</td>
                                        <td class="font-monospace" x-text="detailData?.enrollment?.applicant?.nik || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">NISN</td>
                                        <td class="font-monospace" x-text="detailData?.enrollment?.applicant?.nisn || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Tempat / Tgl Lahir</td>
                                        <td>
                                            <span x-text="detailData?.enrollment?.applicant?.place_of_birth || '-'"></span>, 
                                            <span x-text="detailData?.enrollment?.applicant?.date_of_birth ? formatDateSimple(detailData.enrollment.applicant.date_of_birth) : '-'"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Agama & Kepercayaan</td>
                                        <td x-text="detailData?.enrollment?.applicant?.religion || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Kewarganegaraan</td>
                                        <td x-text="detailData?.enrollment?.applicant?.citizenship || '-'"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary">Kontak & Wali</h6>
                                <table class="table table-borderless align-middle m-0">
                                    <tr>
                                        <td width="35%" class="fw-bold text-muted">Telepon Siswa</td>
                                        <td x-text="detailData?.enrollment?.applicant?.phone || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Nama Orang Tua / Wali</td>
                                        <td x-text="detailData?.enrollment?.applicant?.parent_name || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Telepon Orang Tua / Wali</td>
                                        <td x-text="detailData?.enrollment?.applicant?.parent_phone || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Alamat Rumah</td>
                                        <td x-text="detailData?.enrollment?.applicant?.address || '-'"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Nomor Pendaftaran</td>
                                        <td>
                                            <span class="font-monospace fw-bold text-primary" x-text="detailData?.enrollment?.enrollment_number || 'Belum Terdaftar'"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Jalur / TA</td>
                                        <td>
                                            <span x-text="detailData?.enrollment?.spmb_track?.track_type?.name"></span>
                                            (<span x-text="detailData?.enrollment?.spmb_track?.spmb_configuration?.academic_year?.name"></span>)
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Data Formulir -->
                    <div class="tab-pane" id="tab-formulir" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h6 class="fw-bold text-secondary mb-0">Data Tambahan Formulir Pendaftaran</h6>
                        </div>
                        
                        <template x-if="detailData?.enrollment?.form_data && detailData.enrollment.form_data.length > 0">
                            <div class="row g-3">
                                <template x-for="(item, index) in detailData.enrollment.form_data" :key="item.id">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded shadow-xs border-start border-3 h-100"
                                            :class="{
                                                'border-primary': item.is_valid === null || item.is_valid === 'null',
                                                'border-success bg-success-subtle': String(item.is_valid) === 'true',
                                                'border-danger bg-danger-subtle': String(item.is_valid) === 'false'
                                            }">
                                            
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="d-block text-muted small fw-bold" x-text="item.field?.field_label"></span>
                                                <span class="badge" 
                                                      :class="{
                                                          'bg-secondary': item.is_valid === null || item.is_valid === 'null',
                                                          'bg-success': String(item.is_valid) === 'true',
                                                          'bg-danger': String(item.is_valid) === 'false'
                                                      }"
                                                      x-text="item.is_valid === null || item.is_valid === 'null' ? 'Belum Diperiksa' : (String(item.is_valid) === 'true' ? 'Valid' : 'Tidak Valid')">
                                                </span>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <template x-if="item.field?.field_type === 'file'">
                                                    <a :href="'/' + item.value" target="_blank" class="btn btn-sm btn-outline-primary w-100" x-show="item.value">
                                                        <i class="ti ti-external-link"></i> Lihat Berkas
                                                    </a>
                                                </template>
                                                <template x-if="item.field?.field_type !== 'file'">
                                                    <span class="d-block fw-bold text-dark" x-text="item.value || '-'"></span>
                                                </template>
                                            </div>

                                            <div x-show="String(item.is_valid) === 'false'" class="mt-2 pt-2 border-top border-danger border-opacity-25">
                                                <label class="small text-danger fw-bold mb-1"><i class="ti ti-alert-circle"></i> Catatan Revisi:</label>
                                                <p class="small text-danger mb-0" x-text="item.validation_note || '-'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!detailData?.enrollment?.form_data || detailData.enrollment.form_data.length === 0">
                            <div class="text-center py-4">
                                <i class="ti ti-file-off fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada data tambahan yang diisi.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Tab Keuangan -->
                    <div class="tab-pane" id="tab-keuangan" role="tabpanel">
                        <template x-if="detailData?.enrollment?.invoices && detailData.enrollment.invoices.length > 0">
                            <div class="space-y-4">
                                <template x-for="invoice in detailData.enrollment.invoices" :key="invoice.id">
                                    <div class="card border border-light-subtle shadow-none mb-3">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                            <div>
                                                <span class="fw-bold text-primary font-monospace" x-text="invoice.invoice_number"></span>
                                                <span class="badge bg-light-primary text-primary ms-2" x-text="invoice.category === 'registration' ? 'Biaya Pendaftaran' : 'Biaya Daftar Ulang'"></span>
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
                                                    <span class="fw-bold fs-5 text-dark" x-text="formatRupiah(invoice.total_amount)"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Sudah Dibayar</small>
                                                    <span class="fw-bold fs-5 text-success" x-text="formatRupiah(invoice.paid_amount)"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Sisa Tagihan</small>
                                                    <span class="fw-bold fs-5 text-danger" x-text="formatRupiah(Math.max(0, invoice.total_amount - invoice.paid_amount))"></span>
                                                </div>
                                            </div>

                                            <!-- Invoice Items -->
                                            <h6 class="fw-bold text-secondary mt-3 mb-2"><i class="ti ti-align-left me-1"></i>Komponen Biaya</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered align-middle">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Deskripsi</th>
                                                            <th width="150" class="text-end">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-for="item in invoice.items" :key="item.id">
                                                            <tr>
                                                                <td x-text="item.description"></td>
                                                                <td class="text-end font-monospace" x-text="formatRupiah(item.amount)"></td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Payments List -->
                                            <h6 class="fw-bold text-secondary mt-4 mb-2"><i class="ti ti-history me-1"></i>Riwayat Transaksi Masuk</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped table-bordered align-middle">
                                                    <thead class="bg-light text-muted">
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Metode</th>
                                                            <th class="text-end">Nominal Klaim</th>
                                                            <th class="text-end">Terverifikasi</th>
                                                            <th>Status</th>
                                                            <th>Verifikator</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template x-if="invoice.payments && invoice.payments.length > 0">
                                                            <template x-for="payment in invoice.payments" :key="payment.id">
                                                                <tr>
                                                                    <td x-text="formatDateSimple(payment.created_at)"></td>
                                                                    <td>
                                                                        <span x-html="payment.input_method === 'upload' ? '<i class=\'ti ti-upload text-info me-1\'></i>Upload' : '<i class=\'ti ti-cash text-success me-1\'></i>Tunai'"></span>
                                                                    </td>
                                                                    <td class="text-end font-monospace" x-text="formatRupiah(payment.amount)"></td>
                                                                    <td class="text-end font-monospace fw-bold" x-text="payment.confirmed_amount !== null ? formatRupiah(payment.confirmed_amount) : '-'"></td>
                                                                    <td>
                                                                        <span :class="'badge ' + getPaymentStatusBadge(payment.status)" x-text="getPaymentStatusLabel(payment.status)"></span>
                                                                    </td>
                                                                    <td>
                                                                        <span x-text="payment.confirmed_by ? (payment.confirmed_by_user?.name || 'Admin') : '-'"></span>
                                                                    </td>
                                                                </tr>
                                                            </template>
                                                        </template>
                                                        <template x-if="!invoice.payments || invoice.payments.length === 0">
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">Belum ada transaksi pembayaran masuk.</td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!detailData?.enrollment?.invoices || detailData.enrollment.invoices.length === 0">
                            <div class="text-center py-4">
                                <i class="ti ti-receipt-off fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada invoice/tagihan diterbitkan.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Tab Logs -->
                    <div class="tab-pane" id="tab-logs" role="tabpanel">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary">Riwayat Perubahan Status (Audit Trail)</h6>
                        <div class="position-relative ps-4 py-2 border-start border-primary border-2 ms-3">
                            <template x-if="detailData?.enrollment?.status_logs && detailData.enrollment.status_logs.length > 0">
                                <template x-for="log in detailData.enrollment.status_logs" :key="log.id">
                                    <div class="mb-4 position-relative">
                                        <!-- Dot Indicator -->
                                        <div class="position-absolute start-0 translate-middle-x bg-primary rounded-circle border border-white" style="width: 12px; height: 12px; left: -22px !important; top: 6px;"></div>
                                        
                                        <div class="bg-light p-3 rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-light-primary text-primary" x-text="getEnrollmentStatusLabel(log.to_status)"></span>
                                                <span class="text-muted small" x-text="formatDateSimple(log.changed_at)"></span>
                                            </div>
                                            <p class="mb-1 text-dark" x-text="log.reason || '-'"></p>
                                            <small class="text-muted">
                                                Oleh: <span class="fw-bold" x-text="log.changer?.name || 'Sistem / Pendaftar'"></span>
                                            </small>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template x-if="!detailData?.enrollment?.status_logs || detailData.enrollment.status_logs.length === 0">
                                <div class="text-center py-4 border-0 ps-0">
                                    <p class="text-muted">Belum ada riwayat aktivitas status.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
