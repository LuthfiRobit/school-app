@push('js')
<script>
    function userApp() {
        return {
            table: null,
            editMode: false,
            loading: false,
            filterRole: '',
            formData: {
                id: '',
                name: '',
                username: '',
                email: '',
                password: '',
                roles: []
            },

            init() {
                this.initSelect2Filter();
                this.initDataTable();
            },

            initSelect2Filter() {
                const self = this;
                $('#filter-role').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.filterRole = $(this).val();
                    self.table.ajax.reload();
                });
            },

            initSelect2(el, field) {
                const self = this;
                const isMultiple = $(el).prop('multiple');
                $(el).select2({
                    // Gunakan default theme untuk multiple agar wrapping horizontal bisa dikontrol
                    theme: isMultiple ? 'default' : 'bootstrap-5',
                    placeholder: $(el).data('placeholder'),
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal-user')
                }).on('change', function() {
                    self.formData[field] = $(this).val();
                });
            },

            initDataTable() {
                if ($.fn.DataTable.isDataTable('#user-table')) {
                    $('#user-table').DataTable().destroy();
                }

                this.table = $('#user-table').DataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.settings.user.data') }}",
                        data: (d) => {
                            d.role = this.filterRole;
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                        { data: 'user_info', name: 'name' },
                        { data: 'roles', name: 'roles', orderable: false }
                    ],
                    language: {
                        processing: '<div class="spinner-border text-primary" role="status"></div>'
                    }
                });

                // Event delegation
                $('#user-table').on('click', '.btn-edit', (e) => {
                    const id = $(e.currentTarget).data('id');
                    this.openEditModal(id);
                });

                $('#user-table').on('click', '.btn-delete', (e) => {
                    const id = $(e.currentTarget).data('id');
                    this.confirmDelete(id);
                });

                $('#user-table').on('click', '.btn-reset-password', (e) => {
                    const id = $(e.currentTarget).data('id');
                    this.confirmResetPassword(id);
                });
            },

            resetForm() {
                this.formData = { id: '', name: '', username: '', email: '', password: '', roles: [] };
                $('#user-roles').val(null).trigger('change.select2');
            },

            openCreateModal() {
                this.editMode = false;
                this.resetForm();
                $('#modal-user').modal('show');
            },

            async openEditModal(id) {
                this.editMode = true;
                this.resetForm();
                try {
                    const res = await fetch(`{{ url('admin/settings/user') }}/${id}`);
                    const data = await res.json();
                    this.formData = {
                        id: data.id,
                        name: data.name,
                        username: data.username,
                        email: data.email,
                        password: '',
                        roles: data.roles
                    };
                    
                    // Update Select2
                    $('#user-roles').val(data.roles).trigger('change.select2');
                    
                    $('#modal-user').modal('show');
                } catch (err) {
                    toastr.error('Gagal mengambil data user');
                }
            },

            async submitForm() {
                if (this.formData.roles.length === 0) {
                    toastr.warning('Silakan pilih minimal satu role');
                    return;
                }

                this.loading = true;
                const method = this.editMode ? 'PUT' : 'POST';
                const url = this.editMode 
                    ? `{{ url('admin/settings/user') }}/${this.formData.id}`
                    : `{{ route('admin.settings.user.store') }}`;

                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const data = await res.json();
                    if (res.ok) {
                        toastr.success(data.message);
                        $('#modal-user').modal('hide');
                        this.table.ajax.reload();
                    } else {
                        toastr.error(data.message || 'Terjadi kesalahan');
                    }
                } catch (err) {
                    toastr.error('Terjadi kesalahan pada server');
                } finally {
                    this.loading = false;
                }
            },

            confirmDelete(id) {
                Swal.fire({
                    title: 'Hapus User?',
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea5455',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`{{ url('admin/settings/user') }}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            if (res.ok) {
                                toastr.success(data.message);
                                this.table.ajax.reload();
                            } else {
                                toastr.error(data.message);
                            }
                        } catch (err) {
                            toastr.error('Gagal menghapus user');
                        }
                    }
                });
            },

            confirmResetPassword(id) {
                Swal.fire({
                    title: 'Reset Password?',
                    text: "Password akan di-reset menjadi: password",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f1c40f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`{{ url('admin/settings/user') }}/${id}/reset-password`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            if (res.ok) {
                                toastr.success(data.message);
                            } else {
                                toastr.error(data.message);
                            }
                        } catch (err) {
                            toastr.error('Gagal me-reset password');
                        }
                    }
                });
            }
        }
    }
</script>
@endpush
