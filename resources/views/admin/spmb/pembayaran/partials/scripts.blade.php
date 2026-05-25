<script>
    function paymentApp() {
        return {
            loading: false,
            table: null,
            detailData: null,
            filter: {
                status: '',
                input_method: '',
                category: ''
            },
            confirmForm: {
                id: null,
                claimed_amount: 0,
                claimed_formatted: '',
                confirmed_amount: 0
            },
            rejectForm: {
                id: null,
                claimed_amount: 0,
                claimed_formatted: '',
                rejection_reason: ''
            },
            manualForm: {
                invoice_id: '',
                amount: 0,
                notes: '',
                remAmount: 0
            },

            init() {
                this.initDataTable();
                this.setupEventListeners();
            },

            initDataTable() {
                if ($.fn.DataTable.isDataTable('#paymentTable')) {
                    $('#paymentTable').DataTable().destroy();
                }
                const self = this;
                this.table = $('#paymentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.spmb.pembayaran.data') }}",
                        data: function (d) {
                            d.status = self.filter.status;
                            d.input_method = self.filter.input_method;
                            d.category = self.filter.category;
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'enrollment_number', name: 'invoice.enrollment.enrollment_number' },
                        { data: 'applicant_name', name: 'invoice.enrollment.applicant.full_name' },
                        { data: 'invoice_number', name: 'invoice.invoice_number' },
                        { data: 'invoice_category_label', name: 'invoice.category', className: 'text-center' },
                        { data: 'amount_formatted', name: 'amount', className: 'text-end font-monospace' },
                        { data: 'confirmed_amount_formatted', name: 'confirmed_amount', className: 'text-end font-monospace' },
                        { data: 'input_method_label', name: 'input_method', className: 'text-center' },
                        { data: 'status_badge', name: 'status', className: 'text-center' },
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
                const $table = $('#paymentTable');

                $table.on('click', '.btn-detail', function() {
                    const id = $(this).data('id');
                    self.showDetail(id);
                });

                $table.on('click', '.btn-confirm', function() {
                    const id = $(this).data('id');
                    self.openConfirmModal(id);
                });

                $table.on('click', '.btn-reject', function() {
                    const id = $(this).data('id');
                    self.openRejectModal(id);
                });

                $('#detailModal, #confirmModal, #rejectModal, #manualModal').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                });
            },

            refreshTable() {
                this.table.ajax.reload();
            },

            async showDetail(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.pembayaran.show', ':id') }}`.replace(':id', id));
                    this.detailData = response.data;
                    const modal = new bootstrap.Modal(this.$refs.detailModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat mengambil detail pembayaran', 'error');
                }
            },

            async openConfirmModal(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.pembayaran.show', ':id') }}`.replace(':id', id));
                    const payment = response.data;

                    this.confirmForm = {
                        id: payment.id,
                        claimed_amount: payment.amount,
                        claimed_formatted: payment.amount_formatted,
                        confirmed_amount: payment.amount // Default to claimed
                    };

                    const modal = new bootstrap.Modal(this.$refs.confirmModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat mengambil detail pembayaran', 'error');
                }
            },

            async submitConfirmation() {
                this.loading = true;
                try {
                    const response = await axios.post(
                        `{{ route('admin.spmb.pembayaran.confirm', ':id') }}`.replace(':id', this.confirmForm.id),
                        {
                            confirmed_amount: this.confirmForm.confirmed_amount
                        }
                    );

                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Dikonfirmasi',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance(this.$refs.confirmModal).hide();
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            async openRejectModal(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.pembayaran.show', ':id') }}`.replace(':id', id));
                    const payment = response.data;

                    this.rejectForm = {
                        id: payment.id,
                        claimed_amount: payment.amount,
                        claimed_formatted: payment.amount_formatted,
                        rejection_reason: ''
                    };

                    const modal = new bootstrap.Modal(this.$refs.rejectModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat mengambil detail pembayaran', 'error');
                }
            },

            async submitRejection() {
                this.loading = true;
                try {
                    const response = await axios.post(
                        `{{ route('admin.spmb.pembayaran.reject', ':id') }}`.replace(':id', this.rejectForm.id),
                        {
                            rejection_reason: this.rejectForm.rejection_reason
                        }
                    );

                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Ditolak',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance(this.$refs.rejectModal).hide();
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            openManualPaymentModal() {
                this.manualForm = {
                    invoice_id: '',
                    amount: 0,
                    notes: '',
                    remAmount: 0
                };
                const modal = new bootstrap.Modal(this.$refs.manualModal);
                modal.show();
            },

            onInvoiceSelected(e) {
                const opt = e.target.options[e.target.selectedIndex];
                const rem = parseFloat(opt.getAttribute('data-rem-amount')) || 0;
                this.manualForm.remAmount = rem;
                this.manualForm.amount = rem; // Pre-fill with remaining
            },

            async submitManualPayment() {
                this.loading = true;
                try {
                    const response = await axios.post("{{ route('admin.spmb.pembayaran.manual') }}", {
                        invoice_id: this.manualForm.invoice_id,
                        amount: this.manualForm.amount,
                        notes: this.manualForm.notes
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Dicatat',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Refresh the full page to update the Blade unpaid invoices list
                        window.location.reload();
                    });
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

            formatRupiahSimple(value) {
                return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(value);
            },

            formatDateSimple(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
            },

            getPercentPaid(invoice) {
                if (!invoice) return 0;
                const total = parseFloat(invoice.total_amount) || 0;
                if (total <= 0) return 0;
                const paid = parseFloat(invoice.paid_amount) || 0;
                return Math.min(100, Math.round((paid / total) * 100));
            },

            // Status mappings
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
