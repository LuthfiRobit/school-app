<!-- Mapping Modal -->
<div class="modal fade" id="mappingModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="ti ti-list-details me-2"></i>Kelola Persyaratan Jalur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-fill bg-light m-0 border-bottom-0" id="mappingTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-3" id="fee-tab" data-bs-toggle="tab"
                            data-bs-target="#fee-tab-pane" type="button" role="tab" aria-controls="fee-tab-pane"
                            aria-selected="true">
                            <i class="ti ti-cash me-1"></i> Komponen Biaya
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3" id="test-tab" data-bs-toggle="tab"
                            data-bs-target="#test-tab-pane" type="button" role="tab" aria-controls="test-tab-pane"
                            aria-selected="false">
                            <i class="ti ti-file-pencil me-1"></i> Jenis Ujian
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3" id="form-tab" data-bs-toggle="tab"
                            data-bs-target="#form-tab-pane" type="button" role="tab" aria-controls="form-tab-pane"
                            aria-selected="false">
                            <i class="ti ti-forms me-1"></i> Form Fields
                        </button>
                    </li>
                </ul>

                <div class="tab-content bg-white" id="mappingTabsContent">
                    <!-- Tab Komponen Biaya -->
                    <div class="tab-pane fade show active p-4" id="fee-tab-pane" role="tabpanel"
                        aria-labelledby="fee-tab" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Daftar Biaya <span class="text-primary"
                                        x-text="detailData?.track_name"></span></h6>
                                <small class="text-muted">Atur biaya pendaftaran & daftar ulang untuk jalur ini.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" @click="addFeeRow">
                                <i class="ti ti-plus me-1"></i> Tambah Biaya
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Komponen Biaya</th>
                                        <th width="25%">Kategori</th>
                                        <th width="25%">Nominal (Rp)</th>
                                        <th width="10%">Urutan</th>
                                        <th width="5%" class="text-center"><i class="ti ti-settings"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(fee, index) in trackFees" :key="index">
                                        <tr>
                                            <td>
                                                <select class="form-select form-select-sm"
                                                    x-model="fee.master_fee_component_id"
                                                    x-effect="if(masterFees.length) { $nextTick(() => { $el.value = fee.master_fee_component_id }) }"
                                                    required>
                                                    <option value="">-- Pilih Master Biaya --</option>
                                                    <template x-for="master in masterFees" :key="master.id">
                                                        <option :value="String(master.id)" x-text="master.name">
                                                        </option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm" x-model="fee.category"
                                                    required>
                                                    <option value="registration">Pendaftaran (Awal)</option>
                                                    <option value="re_registration">Daftar Ulang (Lulus)</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    x-model.number="fee.amount" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm text-center"
                                                    x-model.number="fee.display_order" min="0" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-light-danger btn-icon"
                                                    @click="removeFeeRow(index)">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="trackFees.length === 0">
                                        <td colspan="5" class="text-center py-4 text-muted fst-italic">
                                            Belum ada komponen biaya. Klik "Tambah Biaya" untuk memulai.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-info px-4 shadow-sm" @click="saveFees"
                                :disabled="loadingMapping">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"
                                    x-show="loadingMapping"></span>
                                <i class="ti ti-device-floppy me-1" x-show="!loadingMapping"></i> Simpan Biaya
                            </button>
                        </div>
                    </div>

                    <!-- Tab Jenis Ujian -->
                    <div class="tab-pane fade p-4" id="test-tab-pane" role="tabpanel" aria-labelledby="test-tab"
                        tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Daftar Jenis Ujian <span class="text-primary"
                                        x-text="detailData?.track_name"></span></h6>
                                <small class="text-muted">Tentukan jenis tes yang wajib diikuti oleh pendaftar jalur
                                    ini.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-info text-white" @click="addAssessmentRow">
                                <i class="ti ti-plus me-1"></i> Tambah Ujian
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Jenis Ujian</th>
                                        <th width="20%">Bobot (%)</th>
                                        <th width="25%">Passing Score (Min)</th>
                                        <th width="15%">Urutan</th>
                                        <th width="5%" class="text-center"><i class="ti ti-settings"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(assessment, index) in trackAssessments" :key="index">
                                        <tr>
                                            <td>
                                                <select class="form-select form-select-sm"
                                                    x-model="assessment.master_assessment_type_id"
                                                    x-effect="if(masterAssessments.length) { $nextTick(() => { $el.value = assessment.master_assessment_type_id }) }"
                                                    required>
                                                    <option value="">-- Pilih Jenis Ujian --</option>
                                                    <template x-for="master in masterAssessments" :key="master.id">
                                                        <option :value="String(master.id)" x-text="master.name">
                                                        </option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" class="form-control"
                                                        x-model.number="assessment.weight" min="0" max="100" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm"
                                                    x-model.number="assessment.passing_score" min="0" step="0.01">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm text-center"
                                                    x-model.number="assessment.display_order" min="0" required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-light-danger btn-icon"
                                                    @click="removeAssessmentRow(index)">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="trackAssessments.length === 0">
                                        <td colspan="5" class="text-center py-4 text-muted fst-italic">
                                            Belum ada jenis ujian. Klik "Tambah Ujian" untuk memulai.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot x-show="trackAssessments.length > 0">
                                    <tr class="table-light fw-bold">
                                        <td class="text-end">Total Bobot:</td>
                                        <td>
                                            <span
                                                :class="{'text-success': totalWeight === 100, 'text-danger': totalWeight !== 100}"
                                                x-text="totalWeight + '%'"></span>
                                        </td>
                                        <td colspan="3" class="text-muted small fw-normal">
                                            * Total bobot idealnya adalah 100%
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-info px-4 shadow-sm" @click="saveAssessments"
                                :disabled="loadingMapping">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"
                                    x-show="loadingMapping"></span>
                                <i class="ti ti-device-floppy me-1" x-show="!loadingMapping"></i> Simpan Ujian
                            </button>
                        </div>
                    </div>

                    <!-- Tab Form Fields -->
                    <div class="tab-pane fade p-4" id="form-tab-pane" role="tabpanel" aria-labelledby="form-tab"
                        tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fw-bold">Daftar Form Fields <span class="text-primary"
                                        x-text="detailData?.track_name"></span></h6>
                                <small class="text-muted">Tentukan form isian pendaftaran yang harus diisi untuk jalur
                                    ini.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-info text-white" @click="addFormFieldRow">
                                <i class="ti ti-plus me-1"></i> Tambah Field
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Grup Field</th>
                                        <th width="25%">Label Display</th>
                                        <th width="15%">Tipe Input</th>
                                        <th width="20%">Pengaturan Tambahan</th>
                                        <th width="15%" class="text-center">Wajib & Urutan</th>
                                        <th width="10%" class="text-center"><i class="ti ti-settings"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(field, index) in trackFormFields" :key="index">
                                        <tr>
                                            <td class="align-top pt-3">
                                                <select class="form-select form-select-sm" x-model="field.field_group"
                                                    required>
                                                    <option value="data_pribadi">Data Pribadi</option>
                                                    <option value="alamat">Alamat</option>
                                                    <option value="kontak">Kontak</option>
                                                    <option value="orang_tua">Orang Tua</option>
                                                    <option value="data_periodik">Data Periodik</option>
                                                    <option value="lainnya">Lainnya</option>
                                                </select>
                                            </td>
                                            <td class="align-top pt-3">
                                                <input type="text" class="form-control form-control-sm mb-1"
                                                    x-model="field.field_label"
                                                    @input="field.field_name = generateSlug($event.target.value)"
                                                    placeholder="Label (cth: Asal Sekolah)" required>
                                            </td>
                                            <td class="align-top pt-3">
                                                <select class="form-select form-select-sm" x-model="field.field_type"
                                                    required @change="handleFieldTypeChange(field)">
                                                    <option value="text">Text (Singkat)</option>
                                                    <option value="textarea">Textarea (Panjang)</option>
                                                    <option value="number">Number (Angka)</option>
                                                    <option value="date">Date (Tanggal)</option>
                                                    <option value="select">Select (Dropdown)</option>
                                                    <option value="radio">Radio (Pilih Satu)</option>
                                                    <option value="checkbox">Checkbox (Pilihan)</option>
                                                    <option value="file">File (Upload)</option>
                                                </select>
                                            </td>
                                            <td class="align-top pt-3">
                                                <div
                                                    x-show="['select', 'radio', 'checkbox'].includes(field.field_type)">
                                                    <input type="text" class="form-control form-control-sm mb-1"
                                                        x-model="field.field_options_text"
                                                        placeholder="Opsi: Laki-laki, Perempuan">
                                                    <small class="text-muted d-block lh-1"
                                                        style="font-size: 0.7rem;">Pisahkan opsi dengan koma (,)</small>
                                                </div>
                                                <div x-show="field.field_type === 'file'">
                                                    <div class="row g-1 mb-1">
                                                        <div class="col-7">
                                                            <select class="form-select form-select-sm"
                                                                x-model="field.file_types">
                                                                <option value="">-- Pilih --</option>
                                                                <option value="pdf">PDF (*.pdf)</option>
                                                                <option value="jpg,jpeg,png">Image (*.jpg, *.png)
                                                                </option>
                                                                <option value="pdf,jpg,jpeg,png">PDF & Image</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-5">
                                                            <input type="number" class="form-control form-control-sm"
                                                                x-model.number="field.max_file_size"
                                                                placeholder="Max KB">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted d-block lh-1"
                                                        style="font-size: 0.7rem;">Ekstensi file dan Max Size
                                                        (KB)</small>
                                                </div>
                                                <div
                                                    x-show="!['select', 'radio', 'checkbox', 'file'].includes(field.field_type)">
                                                    <span class="text-muted small fst-italic">Tidak ada
                                                        pengaturan</span>
                                                </div>
                                            </td>
                                            <td class="align-top pt-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="small fw-bold">Wajib:</span>
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            x-model="field.is_required">
                                                    </div>
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text px-2 bg-light">Urutan</span>
                                                    <input type="number" class="form-control text-center px-1"
                                                        x-model.number="field.display_order" min="0" required>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-sm btn-light-danger btn-icon"
                                                    @click="removeFormFieldRow(index)">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="trackFormFields.length === 0">
                                        <td colspan="6" class="text-center py-4 text-muted fst-italic">
                                            Belum ada form fields konfigurasi. Klik "Tambah Field" untuk mulai.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-info px-4 shadow-sm" @click="saveFormFields"
                                :disabled="loadingMapping">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"
                                    x-show="loadingMapping"></span>
                                <i class="ti ti-device-floppy me-1" x-show="!loadingMapping"></i> Simpan Form Fields
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>