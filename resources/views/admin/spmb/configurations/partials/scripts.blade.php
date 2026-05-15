<script>
    function configApp() {
        return {
            loading: false,
            modalInstance: null,
            editId: null,
            detailData: null,
            formData: {
                academic_year_id: null,
                academic_year_name: '',
                reg_start_date: '',
                reg_end_date: '',
                total_quota: '',
                status: ''
            },
            table: null,

            init() {
                this.initDataTable();
                this.setupEventListeners();
                // We keep the modal instance for "Detail" view if needed
                const modalEl = document.getElementById('setupModal');
                if (modalEl) {
                    this.modalInstance = new bootstrap.Modal(modalEl);
                }
            },

            initDataTable() {
                if ($.fn.DataTable.isDataTable('#configTable')) {
                    this.table = $('#configTable').DataTable();
                    return;
                }

                this.table = $('#configTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.spmb.configurations.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'academic_year', name: 'name' },
                        { data: 'academic_period', name: 'start_date' },
                        { data: 'spmb_period', name: 'reg_start_date', orderable: false },
                        { data: 'total_quota', name: 'total_quota', orderable: false },
                        { data: 'status', name: 'status', orderable: false, searchable: false }
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

            setupEventListeners() {
                const self = this;
                $('#configTable').on('click', '.btn-setup', function() {
                    const id = $(this).data('id');
                    self.editConfig(id);
                });
            },

            refreshTable() {
                this.table.ajax.reload();
            },

            resetForm() {
                this.formData = {
                    academic_year_id: null,
                    academic_year_name: '',
                    reg_start_date: '',
                    reg_end_date: '',
                    total_quota: '',
                    status: ''
                };
                this.editId = null;
            },

            async editConfig(id) {
                this.loading = true;
                try {
                    const response = await axios.get(`{{ route('admin.spmb.configurations.show', ':id') }}`.replace(':id', id));
                    const data = response.data;
                    
                    this.formData = {
                        academic_year_id: data.academicYear.id,
                        academic_year_name: data.academicYear.name,
                        reg_start_date: data.spmbConfig ? data.spmbConfig.reg_start_date : '',
                        reg_end_date: data.spmbConfig ? data.spmbConfig.reg_end_date : '',
                        total_quota: data.spmbConfig ? data.spmbConfig.total_quota : '',
                        status: data.spmbConfig ? data.spmbConfig.status : 'draft'
                    };
                    
                    this.editId = id;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat memuat data', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async submitForm() {
                this.loading = true;
                const url = `{{ route('admin.spmb.configurations.update', ':id') }}`.replace(':id', this.formData.academic_year_id);
                
                try {
                    const response = await axios.put(url, this.formData);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    this.resetForm();
                    this.table.ajax.reload();
                } catch (error) {
                    const message = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', message, 'error');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
