@push('js')
<script>
    function academicYearApp() {
        return {
            formData: {
                name: '', start_date: '', end_date: '',
                ganjil_start_date: '', ganjil_end_date: '',
                genap_start_date: '', genap_end_date: '',
                semester_active: 'ganjil', is_active: false
            },
            editMode: false, selectedId: null, detailData: null, loading: false, table: null,

            init() {
                this.initTable();
                this.initEvents();
            },

            initTable() {
                if ($.fn.DataTable.isDataTable('#academic-year-table')) {
                    this.table = $('#academic-year-table').DataTable();
                    return;
                }

                this.table = $('#academic-year-table').DataTable({
                    processing: true, serverSide: true,
                    ajax: "{{ route('admin.akademik.tahun-pelajaran.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'period', name: 'period' },
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
            },

            initEvents() {
                const self = this;
                $('#academic-year-table').on('click', '.btn-edit', function() { self.editData($(this).data('id')); });
                $('#academic-year-table').on('click', '.btn-detail', function() { self.showDetail($(this).data('id')); });
                $('#academic-year-table').on('click', '.btn-delete', function() { self.confirmDelete($(this).data('id')); });
                $('#academic-year-table').on('change', '.toggle-status', function() {
                    const id = $(this).data('id');
                    const isChecked = $(this).is(':checked');
                    $(this).prop('checked', !isChecked);
                    self.confirmToggle(id);
                });

                $('#modalDetail').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                });
            },

            confirmToggle(id) {
                Swal.fire({
                    title: 'Ubah Status Aktif?',
                    text: "Tahun pelajaran aktif akan dipindahkan ke data ini!",
                    icon: 'question', showCancelButton: true,
                    confirmButtonText: 'Ya, Aktifkan!', cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) this.toggleStatus(id);
                    else this.refreshTable();
                });
            },

            refreshTable() { this.table.ajax.reload(); },

            resetForm() {
                this.formData = {
                    name: '', start_date: '', end_date: '',
                    ganjil_start_date: '', ganjil_end_date: '',
                    genap_start_date: '', genap_end_date: '',
                    semester_active: 'ganjil', is_active: false
                };
                this.editMode = false;
                this.selectedId = null;
            },

            async saveData() {
                this.loading = true;
                const url = this.editMode 
                    ? `{{ route('admin.akademik.tahun-pelajaran.index') }}/${this.selectedId}` 
                    : "{{ route('admin.akademik.tahun-pelajaran.store') }}";
                const method = this.editMode ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ ...this.formData, _method: method })
                    });
                    const result = await response.json();
                    if (response.ok) {
                        toastr.success(result.message);
                        this.resetForm();
                        this.refreshTable();
                    } else {
                        toastr.error(result.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    toastr.error('Gagal terhubung ke server');
                } finally { this.loading = false; }
            },

            async editData(id) {
                try {
                    const response = await fetch(`{{ route('admin.akademik.tahun-pelajaran.index') }}/${id}`);
                    const data = await response.json();
                    const ganjil = data.semesters.find(s => s.type === 'ganjil');
                    const genap = data.semesters.find(s => s.type === 'genap');
                    const activeSem = data.semesters.find(s => s.is_active);

                    this.formData = {
                        name: data.name,
                        start_date: data.start_date.split('T')[0],
                        end_date: data.end_date.split('T')[0],
                        ganjil_start_date: ganjil?.start_date.split('T')[0],
                        ganjil_end_date: ganjil?.end_date.split('T')[0],
                        genap_start_date: genap?.start_date.split('T')[0],
                        genap_end_date: genap?.end_date.split('T')[0],
                        semester_active: activeSem?.type || 'ganjil',
                        is_active: data.is_active
                    };
                    this.selectedId = id;
                    this.editMode = true;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (error) { toastr.error('Gagal mengambil data'); }
            },

            async showDetail(id) {
                try {
                    const response = await fetch(`{{ route('admin.akademik.tahun-pelajaran.index') }}/${id}`);
                    this.detailData = await response.json();
                    new bootstrap.Modal(document.getElementById('modalDetail')).show();
                } catch (error) { toastr.error('Gagal mengambil detail'); }
            },

            confirmDelete(id) {
                Swal.fire({
                    title: 'Hapus data?', text: "Data akan dihapus permanen!", icon: 'warning',
                    showCancelButton: true, confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
                }).then((result) => { if (result.isConfirmed) this.deleteData(id); });
            },

            async deleteData(id) {
                try {
                    const response = await fetch(`{{ route('admin.akademik.tahun-pelajaran.index') }}/${id}`, {
                        method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if (response.ok) { toastr.success('Data berhasil dihapus'); this.refreshTable(); }
                    else { toastr.error('Gagal menghapus data'); }
                } catch (error) { toastr.error('Kesalahan server'); }
            },

            async toggleStatus(id) {
                try {
                    const response = await fetch(`{{ route('admin.akademik.tahun-pelajaran.index') }}/${id}/toggle`, {
                        method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    if (response.ok) { toastr.info('Status diperbarui'); this.refreshTable(); }
                } catch (error) { toastr.error('Gagal mengubah status'); }
            },

            formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            }
        };
    }
</script>
@endpush
