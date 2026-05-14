<script>
    function assessmentApp() {
        return {
            loading: false,
            isEdit: false,
            editId: null,
            formData: {
                name: '',
                input_type: '',
                description: '',
                is_active: true
            },
            selectedIds: [],
            detailData: null,
            table: null,

            init() {
                this.initDataTable();
                this.setupEventListeners();
            },

            initDataTable() {
                if ($.fn.DataTable.isDataTable('#assessmentTable')) {
                    this.table = $('#assessmentTable').DataTable();
                    return;
                }

                this.table = $('#assessmentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.spmb.master.jenis-penilaian.data') }}",
                    columns: [
                        { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'input_type', name: 'input_type' },
                        { data: 'status', name: 'is_active', orderable: false, searchable: false }
                    ],
                    language: {
                        "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                        "sProcessing":   "Sedang memproses...",
                        "sLengthMenu":   "Tampilkan _MENU_ entri",
                        "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                        "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sInfoPostFix":  "",
                        "sSearch":       "Cari:",
                        "sUrl":          "",
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

            setupEventListeners() {
                const self = this;
                const $table = $('#assessmentTable');
                
                $table.on('click', '.btn-edit', function() {
                    const id = $(this).data('id');
                    self.editData(id);
                });

                $table.on('click', '.btn-detail', function() {
                    const id = $(this).data('id');
                    self.showDetail(id);
                });

                $table.on('click', '.btn-delete', function() {
                    const id = $(this).data('id');
                    self.deleteData(id);
                });

                $table.on('change', '.toggle-status', function() {
                    const id = $(this).data('id');
                    const isChecked = $(this).is(':checked');
                    $(this).prop('checked', !isChecked); 
                    self.confirmToggle(id);
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

                $('#detailModal').on('hidden.bs.modal', function () {
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

            async bulkStatus(status) {
                const statusText = status ? 'Aktifkan' : 'Non-Aktifkan';
                const result = await Swal.fire({
                    title: `${statusText} massal?`,
                    text: `Anda akan mengubah status ${this.selectedIds.length} data menjadi ${statusText.toLowerCase()}.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: `Ya, ${statusText}!`,
                    cancelButtonText: 'Batal'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await axios.post("{{ route('admin.spmb.master.jenis-penilaian.bulk-status') }}", {
                            ids: this.selectedIds,
                            status: status
                        });
                        
                        toastr.success(response.data.message);
                        this.refreshTable();
                    } catch (error) {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat memproses data massal', 'error');
                    }
                }
            },

            confirmToggle(id) {
                Swal.fire({
                    title: 'Ubah Status Aktif?',
                    text: "Apakah Anda yakin ingin mengubah status aktif ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.toggleStatus(id);
                    } else {
                        this.refreshTable();
                    }
                });
            },

            refreshTable() {
                this.table.ajax.reload();
            },

            resetForm() {
                this.formData = {
                    name: '',
                    input_type: '',
                    description: '',
                    is_active: true
                };
                this.isEdit = false;
                this.editId = null;
            },

            async submitForm() {
                this.loading = true;
                const url = this.isEdit 
                    ? `{{ route('admin.spmb.master.jenis-penilaian.update', ':id') }}`.replace(':id', this.editId)
                    : `{{ route('admin.spmb.master.jenis-penilaian.store') }}`;
                
                const method = this.isEdit ? 'PUT' : 'POST';

                try {
                    const response = await axios({
                        method: method,
                        url: url,
                        data: this.formData
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    this.resetForm();
                    this.refreshTable();
                } catch (error) {
                    const message = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', message, 'error');
                } finally {
                    this.loading = false;
                }
            },

            async editData(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.master.jenis-penilaian.show', ':id') }}`.replace(':id', id));
                    this.formData = {
                        name: response.data.name,
                        input_type: response.data.input_type,
                        description: response.data.description,
                        is_active: response.data.is_active
                    };
                    this.isEdit = true;
                    this.editId = id;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat mengambil data detail', 'error');
                }
            },

            async showDetail(id) {
                try {
                    const response = await axios.get(`{{ route('admin.spmb.master.jenis-penilaian.show', ':id') }}`.replace(':id', id));
                    this.detailData = response.data;
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat mengambil data detail', 'error');
                }
            },

            async deleteData(id) {
                const result = await Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await axios.delete(`{{ route('admin.spmb.master.jenis-penilaian.destroy', ':id') }}`.replace(':id', id));
                        Swal.fire('Terhapus!', response.data.message, 'success');
                        this.refreshTable();
                    } catch (error) {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                }
            },

            async toggleStatus(id) {
                try {
                    const response = await axios.post(`{{ route('admin.spmb.master.jenis-penilaian.toggle-status', ':id') }}`.replace(':id', id));
                    toastr.success(response.data.message);
                    this.refreshTable();
                } catch (error) {
                    toastr.error('Gagal mengubah status');
                    this.refreshTable();
                }
            },

            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return new Intl.DateTimeFormat('id-ID', {
                    dateStyle: 'medium',
                    timeStyle: 'short'
                }).format(date);
            }
        }
    }
</script>
