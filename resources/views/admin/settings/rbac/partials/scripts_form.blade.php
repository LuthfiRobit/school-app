@push('js')
<script>
    function rbacFormApp() {
        return {
            loading: false,
            editMode: @json(isset($id)),
            formData: {
                id: '{{ $id ?? "" }}',
                name: '',
                scope: '',
                permissions: []
            },
            groupedPermissions: {},
            allPermissionSlugs: [],

            async init() {
                await this.fetchPermissions();
                if (this.editMode) {
                    await this.fetchRoleData();
                }
            },

            initSelect2(el, field) {
                const self = this;
                $(el).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(el).data('placeholder'),
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.formData[field] = $(this).val();
                });

                // Set initial value if exist
                if (this.formData[field]) {
                    $(el).val(this.formData[field]).trigger('change.select2');
                }
            },

            async fetchPermissions() {
                try {
                    const res = await fetch('{{ route('admin.settings.rbac.permissions') }}');
                    const json = await res.json();
                    this.groupedPermissions = json.data;
                    
                    // Collect all slugs for toggleAll from nested structure
                    this.allPermissionSlugs = [];
                    Object.values(this.groupedPermissions).forEach(module => {
                        Object.values(module).forEach(resource => {
                            resource.forEach(p => this.allPermissionSlugs.push(p.slug));
                        });
                    });
                } catch (err) {
                    toastr.error('Gagal mengambil daftar hak akses');
                }
            },

            async syncPermissions() {
                const result = await Swal.fire({
                    title: 'Sinkronisasi Permission?',
                    text: "Sistem akan memindai ulang rute untuk memperbarui daftar hak akses.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Sinkronkan!',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;

                this.loading = true;
                try {
                    const res = await fetch('{{ route('admin.settings.rbac.sync') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    
                    if (res.ok) {
                        toastr.success(data.message);
                        await this.fetchPermissions(); // Refresh list
                    } else {
                        toastr.error(data.message || 'Gagal sinkronisasi');
                    }
                } catch (err) {
                    toastr.error('Terjadi kesalahan pada server');
                } finally {
                    this.loading = false;
                }
            },

            async fetchRoleData() {
                try {
                    const res = await fetch('{{ url('admin/settings/rbac') }}/' + this.formData.id);
                    const data = await res.json();
                    this.formData.name = data.name;
                    this.formData.scope = data.scope;
                    this.formData.permissions = data.permissions;

                    // Trigger select2 update
                    $('.select2-alpine').each(function() {
                        const field = $(this).attr('x-init').match(/'([^']+)'/)[1];
                        if (field === 'scope') {
                            $(this).val(data.scope).trigger('change.select2');
                        }
                    });
                } catch (err) {
                    toastr.error('Gagal mengambil data role');
                }
            },

            toggleAll(event) {
                if (event.target.checked) {
                    this.formData.permissions = [...this.allPermissionSlugs];
                } else {
                    this.formData.permissions = [];
                }
            },

            async submitForm() {
                if (!this.formData.scope) {
                    toastr.warning('Silakan pilih Scope terlebih dahulu');
                    return;
                }

                this.loading = true;
                const url = this.editMode 
                    ? '{{ url('admin/settings/rbac') }}/' + this.formData.id 
                    : '{{ route('admin.settings.rbac.store') }}';
                
                const method = this.editMode ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const res = await response.json();

                    if (response.ok) {
                        toastr.success(res.message);
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.settings.rbac.index') }}";
                        }, 1000);
                    } else {
                        toastr.error(res.message || 'Terjadi kesalahan validasi');
                        this.loading = false;
                    }
                } catch (err) {
                    toastr.error('Terjadi kesalahan pada server');
                    this.loading = false;
                }
            }
        }
    }
</script>
@endpush
