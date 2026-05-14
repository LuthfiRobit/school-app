<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="mb-0">Daftar Hak Akses</h5>
    <button class="btn btn-secondary d-inline-flex align-items-center" @click="openPermissionModal()">
        <i class="ti ti-plus me-1"></i> Tambah Permission
    </button>
</div>

<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="table-light">
            <tr>
                <th width="50">#</th>
                <th>Nama Permission</th>
                <th>Slug</th>
                <th>Grup</th>
                <th width="150" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(perm, index) in permissions" :key="index">
                <tr>
                    <td x-text="index + 1"></td>
                    <td><strong x-text="perm.name"></strong></td>
                    <td><code x-text="perm.slug"></code></td>
                    <td><span class="badge bg-light-info text-info" x-text="perm.group"></span></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-icon btn-link-secondary" @click="editPermission(perm)" title="Edit">
                                <i class="ti ti-edit fs-5"></i>
                            </button>
                            <button class="btn btn-icon btn-link-danger" @click="confirmDeletePermission(perm.id)" title="Hapus">
                                <i class="ti ti-trash fs-5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>
