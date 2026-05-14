@push('js')
<script>
    function rbacApp() {
        return {
            loading: false,
            editMode: false,
            
            // Dummy Data
            roles: [
                { id: 1, name: 'Developer', slug: 'developer', permissions: ['all-access'] },
                { id: 2, name: 'Administrator', slug: 'administrator', permissions: ['view-dashboard', 'manage-users', 'manage-school'] },
                { id: 3, name: 'Guru', slug: 'guru', permissions: ['view-dashboard', 'manage-grades'] }
            ],
            
            permissions: [
                { id: 1, name: 'Lihat Dashboard', slug: 'view-dashboard', group: 'Umum' },
                { id: 2, name: 'Kelola User', slug: 'manage-users', group: 'Sistem' },
                { id: 3, name: 'Kelola Sekolah', slug: 'manage-school', group: 'Sekolah' },
                { id: 4, name: 'Kelola Nilai', slug: 'manage-grades', group: 'Akademik' },
                { id: 5, name: 'Akses Penuh', slug: 'all-access', group: 'Sistem' }
            ],

            // Form States
            roleForm: { id: null, name: '', slug: '', permissions: [] },
            permissionForm: { id: null, name: '', slug: '', group: '' },

            // Role Actions
            openRoleModal() {
                this.editMode = false;
                this.roleForm = { id: null, name: '', slug: '', permissions: [] };
                new bootstrap.Modal(document.getElementById('roleModal')).show();
            },
            editRole(role) {
                this.editMode = true;
                this.roleForm = { ...role, permissions: [...role.permissions] };
                new bootstrap.Modal(document.getElementById('roleModal')).show();
            },
            saveRole() {
                this.loading = true;
                setTimeout(() => {
                    toastr.success('Role berhasil disimpan (Mockup)');
                    bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
                    this.loading = false;
                }, 1000);
            },
            confirmDeleteRole(id) {
                Swal.fire({
                    title: 'Hapus Role?',
                    text: "User dengan role ini mungkin kehilangan akses!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        toastr.info('Aksi hapus berhasil dipicu (Mockup)');
                    }
                });
            },

            // Permission Actions
            openPermissionModal() {
                this.editMode = false;
                this.permissionForm = { id: null, name: '', slug: '', group: '' };
                new bootstrap.Modal(document.getElementById('permissionModal')).show();
            },
            editPermission(perm) {
                this.editMode = true;
                this.permissionForm = { ...perm };
                new bootstrap.Modal(document.getElementById('permissionModal')).show();
            },
            savePermission() {
                this.loading = true;
                setTimeout(() => {
                    toastr.success('Permission berhasil disimpan (Mockup)');
                    bootstrap.Modal.getInstance(document.getElementById('permissionModal')).hide();
                    this.loading = false;
                }, 1000);
            },
            confirmDeletePermission(id) {
                Swal.fire({
                    title: 'Hapus Permission?',
                    text: "Aksi ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        toastr.info('Aksi hapus berhasil dipicu (Mockup)');
                    }
                });
            }
        }
    }
</script>
@endpush
