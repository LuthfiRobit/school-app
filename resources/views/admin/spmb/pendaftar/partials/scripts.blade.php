<script>
    function enrollmentApp() {
        return {
            loading: false,
            table: null,
            selectedIds: [],
            detailData: null,
            validationData: null,
            filter: {
                academic_year_id: '',
                spmb_track_id: '',
                status: ''
            },
            statusForm: {
                id: null,
                name: '',
                currentLabel: '',
                currentBadge: '',
                status: '',
                reason: '',
                allowedTransitions: []
            },
            bulkStatusForm: {
                status: '',
                reason: ''
            },

            init() {
                this.initDataTable();
                this.setupEventListeners();
            },

            initDataTable() {
                if ($.fn.DataTable.isDataTable('#enrollmentTable')) {
                    $('#enrollmentTable').DataTable().destroy();
                }
                const self = this;
                this.table = $('#enrollmentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.spmb.pendaftar.data') }}",
                        data: function (d) {
                            d.academic_year_id = self.filter.academic_year_id;
                            d.spmb_track_id = self.filter.spmb_track_id;
                            d.status = self.filter.status;
                        }
                    },
                    columns: [
                        { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'enrollment_number', name: 'enrollment_number' },
                        { data: 'full_name', name: 'applicant.full_name' },
                        { data: 'track_name', name: 'spmbTrack.trackType.name' },
                        { data: 'academic_year', name: 'spmbTrack.spmbConfiguration.academicYear.name' },
                        { data: 'status_badge', name: 'status', orderable: false },
                        { data: 'created_at_formatted', name: 'created_at' }
                    ],
                    language: {
                        "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                        "sProcessing":   "Sedang memproses...",
                        "sLengthMenu":   "Tampilkan _MENU_ entri",
                        "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                        "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sSearch":       "Cari:",
                        "oPaginate": {
                            "sFirst":    "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext":     "Selanjutnya",
                            "sLast":     "Terakhir"
                        }
                    }
                });

                this.table.on('draw', () => {
                    this.selectedIds = [];
                    $('#checkAll').prop('checked', false);
                });
            },

            initSelect2(el, field) {
                const self = this;
                $(el).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(el).data('placeholder'),
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.filter[field] = $(this).val() || '';
                    self.refreshTable();
                });

                if (this.filter[field]) {
                    $(el).val(this.filter[field]).trigger('change.select2');
                }
            },

            setupEventListeners() {
                const self = this;
                const $table = $('#enrollmentTable');

                $table.on('click', '.btn-detail', function() {
                    const id = $(this).data('id');
                    self.showDetail(id);
                });

                $table.on('click', '.btn-status', function() {
                    const id = $(this).data('id');
                    self.openStatusModal(id);
                });

                $table.on('click', '.btn-validate', function() {
                    const id = $(this).data('id');
                    self.openValidationModal(id);
                });

                $table.on('change', '.check-item', function() {
                    const id = $(this).val();
                    if ($(this).is(':checked')) {
                        if (!self.selectedIds.includes(id)) {
                            self.selectedIds.push(id);
                        }
                    } else {
                        self.selectedIds = self.selectedIds.filter(item => item !== id);
                    }

                    const allChecked = $('.check-item:checked').length === $('.check-item').length && $('.check-item').length > 0;
                    $('#checkAll').prop('checked', allChecked);
                });

                // Clean modal backdrops on close
                $('#detailModal, #statusModal, #bulkStatusModal, #validationModal').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                });
            },

            toggleAll(e) {
                const isChecked = e.target.checked;
                this.selectedIds = [];
                $('.check-item').prop('checked', isChecked);
                
                if (isChecked) {
                    $('.check-item').each((i, el) => {
                        this.selectedIds.push($(el).val());
                    });
                }
            },

            refreshTable() {
                this.table.ajax.reload();
            },

            async showDetail(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.pendaftar.show', ':id') }}`.replace(':id', id));
                    this.detailData = response.data;
                    const modal = new bootstrap.Modal(this.$refs.detailModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat mengambil detail pendaftar', 'error');
                }
            },

            async openStatusModal(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.pendaftar.show', ':id') }}`.replace(':id', id));
                    const enrollment = response.data.enrollment;
                    
                    this.statusForm = {
                        id: enrollment.id,
                        name: enrollment.applicant?.full_name || '',
                        currentLabel: this.getEnrollmentStatusLabel(enrollment.status),
                        currentBadge: this.getEnrollmentStatusBadge(enrollment.status),
                        status: '',
                        reason: '',
                        allowedTransitions: response.data.allowed_transitions
                    };

                    const modal = new bootstrap.Modal(this.$refs.statusModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat memproses transisi status', 'error');
                }
            },

            async submitStatusChange() {
                this.loading = true;
                try {
                    const response = await axios.post(
                        `{{ route('admin.spmb.pendaftar.status', ':id') }}`.replace(':id', this.statusForm.id),
                        {
                            status: this.statusForm.status,
                            reason: this.statusForm.reason
                        }
                    );

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance(this.$refs.statusModal).hide();
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            async openValidationModal(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.pendaftar.show', ':id') }}`.replace(':id', id));
                    this.validationData = response.data;
                    const modal = new bootstrap.Modal(this.$refs.validationModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat mengambil detail pendaftar', 'error');
                }
            },

            async submitValidation() {
                this.loading = true;
                try {
                    const validations = this.validationData.enrollment.form_data.map(item => ({
                        id: item.id,
                        is_valid: item.is_valid,
                        validation_note: item.validation_note
                    }));

                    const response = await axios.post(
                        `{{ route('admin.spmb.pendaftar.validate-form', ':id') }}`.replace(':id', this.validationData.enrollment.id),
                        { validations: validations }
                    );

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.data.message + (response.data.auto_drafted ? ' Status pendaftar telah dikembalikan ke Draf.' : ''),
                    });

                    bootstrap.Modal.getInstance(this.$refs.validationModal).hide();
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem saat menyimpan validasi';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            openBulkStatusModal() {
                this.bulkStatusForm = {
                    status: '',
                    reason: ''
                };
                const modal = new bootstrap.Modal(this.$refs.bulkStatusModal);
                modal.show();
            },

            async submitBulkStatusChange() {
                this.loading = true;
                try {
                    const response = await axios.post("{{ route('admin.spmb.pendaftar.bulk-status') }}", {
                        ids: this.selectedIds,
                        status: this.bulkStatusForm.status,
                        reason: this.bulkStatusForm.reason
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance(this.$refs.bulkStatusModal).hide();
                    this.selectedIds = [];
                    $('#checkAll').prop('checked', false);
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // Formatting helpers
            formatRupiah(value) {
                if (value === null || value === undefined) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(value);
            },

            formatDateSimple(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
            },

            // Mappings matching back-end enums
            getEnrollmentStatusLabel(status) {
                const labels = {
                    draft: 'Draft',
                    registered: 'Terdaftar',
                    waiting_payment_reg: 'Menunggu Pembayaran Formulir',
                    verified_reg: 'Pembayaran Formulir Terverifikasi',
                    in_review: 'Sedang Dinilai',
                    passed: 'Lulus Seleksi',
                    waiting_list: 'Cadangan (Waiting List)',
                    rejected: 'Tidak Lulus',
                    waiting_payment_final: 'Menunggu Pembayaran Daftar Ulang',
                    settled: 'Lunas Daftar Ulang',
                    permanent_student: 'Siswa Tetap'
                };
                return labels[status] || status;
            },

            getEnrollmentStatusBadge(status) {
                const badges = {
                    draft: 'bg-light-secondary text-secondary',
                    registered: 'bg-light-info text-info',
                    waiting_payment_reg: 'bg-light-warning text-warning',
                    verified_reg: 'bg-light-success text-success',
                    in_review: 'bg-light-primary text-primary',
                    passed: 'bg-light-success text-success',
                    waiting_list: 'bg-light-warning text-warning',
                    rejected: 'bg-light-danger text-danger',
                    waiting_payment_final: 'bg-light-warning text-warning',
                    settled: 'bg-light-success text-success',
                    permanent_student: 'bg-light-success text-success'
                };
                return badges[status] || 'bg-light';
            },

            getInvoiceStatusLabel(status) {
                const labels = {
                    unpaid: 'Belum Dibayar',
                    partial: 'Dibayar Sebagian',
                    paid: 'Lunas',
                    cancelled: 'Dibatalkan'
                };
                return labels[status] || status;
            },

            getInvoiceStatusBadge(status) {
                const badges = {
                    unpaid: 'bg-light-danger text-danger',
                    partial: 'bg-light-warning text-warning',
                    paid: 'bg-light-success text-success',
                    cancelled: 'bg-light-secondary text-secondary'
                };
                return badges[status] || 'bg-light';
            },

            getPaymentStatusLabel(status) {
                const labels = {
                    pending: 'Menunggu Verifikasi',
                    confirmed: 'Dikonfirmasi',
                    rejected: 'Ditolak'
                };
                return labels[status] || status;
            },

            getPaymentStatusBadge(status) {
                const badges = {
                    pending: 'bg-light-warning text-warning',
                    confirmed: 'bg-light-success text-success',
                    rejected: 'bg-light-danger text-danger'
                };
                return badges[status] || 'bg-light';
            }
        };
    }
</script>
