<script>
    function daftarUlangApp() {
        return {
            loading: false,
            detailData: {},
            selectedId: null,
            selectedApplicantName: '',
            dataTable: null,
            filter: {
                academic_year_id: '',
                spmb_track_id: '',
                status: ''
            },
            
            initPage() {
                this.initTable();
                this.setupEventListeners();
            },

            initTable() {
                if ($.fn.DataTable.isDataTable('#daftarUlangTable')) {
                    $('#daftarUlangTable').DataTable().destroy();
                }
                const self = this;
                this.dataTable = $('#daftarUlangTable').DataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.spmb.daftar-ulang.data') }}",
                        type: 'GET',
                        data: function (d) {
                            d.academic_year_id = self.filter.academic_year_id;
                            d.spmb_track_id = self.filter.spmb_track_id;
                            d.status = self.filter.status;
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'applicant_name', name: 'applicant.full_name' },
                        { data: 'track_name', name: 'spmbTrack.trackType.name' },
                        { data: 'status_label', name: 'status' },
                        { data: 'total_amount', name: 'total_amount', searchable: false },
                        { data: 'paid_amount', name: 'paid_amount', searchable: false },
                        { 
                            data: 'progress', 
                            name: 'progress', 
                            searchable: false,
                            render: function(data, type, row) {
                                let color = 'danger';
                                if (data >= 100) color = 'success';
                                else if (data > 50) color = 'warning';
                                
                                return `<div class="d-flex align-items-center">
                                            <span class="me-2">${data}%</span>
                                            <div class="progress" style="width: 80px; height: 6px; margin-bottom: 0;">
                                                <div class="progress-bar bg-${color}" role="progressbar" style="width: ${data}%" aria-valuenow="${data}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>`;
                            }
                        }
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
                const $table = $('#daftarUlangTable');
                
                $table.on('click', '.btn-detail', function() {
                    const id = $(this).data('id');
                    self.showDetail(id);
                });

                $table.on('click', '.btn-finalize', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name') || '';
                    self.selectedId = id;
                    self.selectedApplicantName = name;
                    const modal = new bootstrap.Modal(document.getElementById('modalFinalisasi'));
                    modal.show();
                });

                // Clean modal backdrops on close
                $('#modalDetail, #modalFinalisasi').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                });
            },

            refreshTable() {
                if (this.dataTable) {
                    this.dataTable.ajax.reload(null, false);
                }
            },

            async showDetail(id) {
                try {
                    const response = await axios.get(`/admin/spmb/daftar-ulang/${id}`);
                    this.detailData = response.data;
                    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Gagal mengambil data detail', 'error');
                }
            },

            async submitFinalisasi() {
                if (!this.selectedId) return;
                
                this.loading = true;
                try {
                    const response = await axios.post(`/admin/spmb/daftar-ulang/${this.selectedId}/finalize`);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance(document.getElementById('modalFinalisasi')).hide();
                    this.refreshTable();
                } catch (error) {
                    let msg = error.response?.data?.message || 'Terjadi kesalahan saat memfinalisasi';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            formatRupiah(number) {
                if (number === null || number === undefined) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(number);
            },
            
            formatDate(dateString) {
                if (!dateString) return '-';
                const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                return new Date(dateString).toLocaleDateString('id-ID', options);
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
        }
    }
</script>
