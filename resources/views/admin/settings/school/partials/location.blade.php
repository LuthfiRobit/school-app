<!-- Lokasi dan Kontak -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="ti ti-map-pin me-2 text-success"></i>Lokasi dan Kontak</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Alamat Lengkap</label>
                    <textarea class="form-control" rows="2" x-model="formData.address" placeholder="Jalan, No, RT/RW, Desa/Kel, Kec, Kab, Prov"></textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Lintang (Latitude)</label>
                    <input type="text" class="form-control" x-model="formData.latitude" placeholder="Contoh: -6.175392">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Bujur (Longitude)</label>
                    <input type="text" class="form-control" x-model="formData.longitude" placeholder="Contoh: 106.827153">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">WhatsApp</label>
                    <input type="text" class="form-control" x-model="formData.whatsapp" placeholder="628xxxxxxxxxx">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Telepon Kantor</label>
                    <input type="text" class="form-control" x-model="formData.phone" placeholder="021-xxxxxxx">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Email Sekolah</label>
                    <input type="email" class="form-control" x-model="formData.email" placeholder="contoh@sekolah.sch.id">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Website</label>
                    <input type="url" class="form-control" x-model="formData.website" placeholder="https://www.sekolah.sch.id">
                </div>
            </div>
        </div>
    </div>
</div>
