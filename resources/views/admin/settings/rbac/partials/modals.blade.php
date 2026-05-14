<!-- Role Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" x-text="editMode ? 'Edit Role' : 'Tambah Role Baru'"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="roleForm" @submit.prevent="saveRole()">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Role</label>
                        <input type="text" class="form-control" x-model="roleForm.name" placeholder="Contoh: Administrator" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Slug</label>
                        <input type="text" class="form-control" x-model="roleForm.slug" placeholder="contoh: administrator" required>
                        <div class="form-text">Gunakan huruf kecil dan tanda hubung (-).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Permissions</label>
                        <div class="row g-2 overflow-auto" style="max-height: 200px;">
                            <template x-for="perm in permissions" :key="perm.id">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" :value="perm.slug" :id="'chk-'+perm.id" x-model="roleForm.permissions">
                                        <label class="form-check-label small" :for="'chk-'+perm.id" x-text="perm.name"></label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="roleForm" class="btn btn-primary" :disabled="loading">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading"><i class="ti ti-loader rotate"></i> Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" x-text="editMode ? 'Edit Permission' : 'Tambah Permission Baru'"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="permissionForm" @submit.prevent="savePermission()">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Permission</label>
                        <input type="text" class="form-control" x-model="permissionForm.name" placeholder="Contoh: Lihat Dashboard" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Slug</label>
                        <input type="text" class="form-control" x-model="permissionForm.slug" placeholder="contoh: view-dashboard" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grup</label>
                        <input type="text" class="form-control" x-model="permissionForm.group" placeholder="Contoh: Akademik" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="permissionForm" class="btn btn-secondary" :disabled="loading">
                    <span x-show="!loading">Simpan</span>
                    <span x-show="loading"><i class="ti ti-loader rotate"></i> Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
