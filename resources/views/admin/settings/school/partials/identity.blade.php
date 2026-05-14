<!-- Identitas Utama & Legalitas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="ti ti-info-circle me-2 text-primary"></i>Identitas Utama & Legalitas</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Nama Sekolah</label>
                    <input type="text" class="form-control" x-model="formData.school_name" placeholder="Masukkan nama sekolah lengkap" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">NPSN</label>
                    <input type="text" class="form-control" x-model="formData.npsn" placeholder="8 digit angka">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Jenjang Pendidikan</label>
                    <select class="form-select select2-alpine" 
                        x-init="initSelect2($el, 'education_level')"
                        data-placeholder="Pilih Jenjang">
                        <option value=""></option>
                        @foreach($enums['jenjang'] as $jenjang)
                            <option value="{{ $jenjang->value }}">{{ $jenjang->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Status Sekolah</label>
                    <select class="form-select select2-alpine" 
                        x-init="initSelect2($el, 'school_status')"
                        data-placeholder="Pilih Status">
                        @foreach($enums['status'] as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Status Kepemilikan</label>
                    <select class="form-select select2-alpine" 
                        x-init="initSelect2($el, 'ownership_status')"
                        data-placeholder="Pilih Kepemilikan">
                        <option value=""></option>
                        @foreach($enums['kepemilikan'] as $milik)
                            <option value="{{ $milik->value }}">{{ $milik->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">SK Pendirian</label>
                    <input type="text" class="form-control" x-model="formData.establishment_sk" placeholder="Nomor SK Pendirian">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Tgl Pendirian</label>
                    <input type="date" class="form-control" x-model="formData.establishment_date">
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">SK Izin Operasional</label>
                    <input type="text" class="form-control" x-model="formData.operational_sk" placeholder="Nomor SK Izin Operasional">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">NPWP Sekolah</label>
                    <input type="text" class="form-control" x-model="formData.tax_id" placeholder="00.000.000.0-000.000">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Akreditasi</label>
                    <select class="form-select select2-alpine" 
                        x-init="initSelect2($el, 'accreditation')"
                        data-placeholder="Pilih Akreditasi">
                        <option value=""></option>
                        @foreach($enums['akreditasi'] as $akre)
                            <option value="{{ $akre->value }}">{{ $akre->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Tgl Kadaluarsa</label>
                    <input type="date" class="form-control" x-model="formData.accreditation_expiry_date">
                </div>
            </div>
        </div>
    </div>
</div>
