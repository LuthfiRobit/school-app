@push('js')
<script>
    function rbacIndexApp() {
        return {
            table: null,
            filterScope: '',

            init() {
                this.initSelect2();
                this.initDataTable();
            },

            initSelect2() {
                const self = this;
                $('#filter-scope').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.filterScope = $(this).val();
                    self.refreshTable();
                });
            },

            initDataTable() {
                this.table = $('#role-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.settings.rbac.data') }}",
                        data: (d) => {
                            d.scope = this.filterScope;
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'role_name', name: 'name' },
                        { data: 'permissions', name: 'permissions', orderable: false }
                    ],
                    language: {
                        processing: '<div class="spinner-border text-primary" role="status"></div>'
                    }
                });

                // Event delegation untuk tombol hapus
                $('#role-table').on('click', '.btn-delete', (e) => {
                    const id = $(e.currentTarget).data('id');
                    this.confirmDeleteRole(id);
                });
            },

            refreshTable() {
                this.table.ajax.reload();
            },

            confirmDeleteRole(id) {
                Swal.fire({
                    title: 'Hapus Role?',
                    text: "User yang memiliki role ini mungkin tidak dapat akses fitur terkait!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('{{ url('admin/settings/rbac') }}/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(res => {
                            if (res.message) {
                                toastr.success(res.message);
                                this.refreshTable();
                            } else {
                                toastr.error('Terjadi kesalahan');
                            }
                        })
                        .catch(err => toastr.error('Gagal menghapus role'));
                    }
                });
            }
        }
    }
</script>
@endpush
