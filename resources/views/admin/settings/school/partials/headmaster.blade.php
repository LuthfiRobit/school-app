<!-- Pejabat & Aset Visual -->
<div class="row g-4">
    <!-- Pejabat / Penandatangan -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="ti ti-user-check me-2 text-info"></i>Pejabat & Penandatangan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nama Kepala Sekolah</label>
                            <input type="text" class="form-control" x-model="formData.headmaster_name" placeholder="Nama lengkap & gelar">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">NIP Kepala Sekolah</label>
                            <input type="text" class="form-control" x-model="formData.headmaster_nip" placeholder="18 digit angka">
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nama Bendahara</label>
                            <input type="text" class="form-control" x-model="formData.treasurer_name" placeholder="Nama lengkap & gelar">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">NIP Bendahara</label>
                            <input type="text" class="form-control" x-model="formData.treasurer_nip" placeholder="18 digit angka">
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nama Operator</label>
                            <input type="text" class="form-control" x-model="formData.operator_name" placeholder="Nama lengkap & gelar">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">NIP Operator</label>
                            <input type="text" class="form-control" x-model="formData.operator_nip" placeholder="18 digit angka">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aset Visual (Logo, Stempel, Banner) -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="ti ti-photo me-2 text-warning"></i>Aset Visual & Gambar</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-6 text-center border-end">
                        <label class="form-label fw-bold d-block">Logo Sekolah</label>
                        <div class="mb-3">
                            <template x-if="previews.logo || formData.logo_url">
                                <img :src="previews.logo || formData.logo_url" class="img-thumbnail mb-2" style="max-height: 100px;">
                            </template>
                            <template x-if="!previews.logo && !formData.logo_url">
                                <div class="bg-light rounded p-4 mb-2 text-muted"><i class="ti ti-photo-off fs-1"></i></div>
                            </template>
                        </div>
                        <input type="file" class="form-control form-control-sm" @change="handleFileUpload($event, 'logo')">
                    </div>
                    
                    <div class="col-6 text-center">
                        <label class="form-label fw-bold d-block">Stempel Resmi</label>
                        <div class="mb-3">
                            <template x-if="previews.stamp || formData.stamp_url">
                                <img :src="previews.stamp || formData.stamp_url" class="img-thumbnail mb-2" style="max-height: 100px;">
                            </template>
                            <template x-if="!previews.stamp && !formData.stamp_url">
                                <div class="bg-light rounded p-4 mb-2 text-muted"><i class="ti ti-photo-off fs-1"></i></div>
                            </template>
                        </div>
                        <input type="file" class="form-control form-control-sm" @change="handleFileUpload($event, 'stamp')">
                    </div>

                    <div class="col-12 text-center border-top pt-3">
                        <label class="form-label fw-bold d-block">Foto Profil / Banner Sekolah</label>
                        <div class="mb-3 text-center">
                            <template x-if="previews.profile_image || formData.profile_image_url">
                                <img :src="previews.profile_image || formData.profile_image_url" class="img-thumbnail mb-2" style="max-width: 100%; max-height: 150px;">
                            </template>
                            <template x-if="!previews.profile_image && !formData.profile_image_url">
                                <div class="bg-light rounded p-3 mb-2 text-muted small">Belum ada foto profil / banner</div>
                            </template>
                        </div>
                        <input type="file" class="form-control form-control-sm" @change="handleFileUpload($event, 'profile_image')">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
