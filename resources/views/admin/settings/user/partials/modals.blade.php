<div class="modal fade" id="modal-user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="ti me-2" :class="editMode ? 'ti-edit text-primary' : 'ti-user-plus text-success'"></i>
                    <span x-text="editMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form @submit.prevent="submitForm()">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" x-model="formData.name"
                                placeholder="Nama lengkap user" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control" x-model="formData.username" placeholder="username"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" x-model="formData.email"
                                placeholder="email@school.id" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" x-model="formData.password"
                                :placeholder="editMode ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter'"
                                :required="!editMode">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Pilih Role</label>
                            <select class="form-select select2-alpine" id="user-roles"
                                x-init="initSelect2($el, 'roles')" multiple
                                data-placeholder="Pilih satu atau lebih role">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}
                                        ({{ $role->scope->label() ?? 'General' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span x-show="!loading"><i class="ti ti-device-floppy me-1"></i> Simpan</span>
                        <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>