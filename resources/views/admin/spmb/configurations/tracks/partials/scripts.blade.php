<script>
    function trackApp() {
        return {
            loading: false,
            editId: null,
            configurationId: '{{ $spmbConfig->id }}',
            mappingModal: null,
            formData: {
                master_track_type_id: '',
                quota: '',
                registration_fee: 0,
                payment_mode: 'PRE_PAYMENT',
                allow_carryover: false,
                announcement_date: '',
                status: 'active'
            },
            masterFees: [],
            trackFees: [],
            masterAssessments: [],
            trackAssessments: [],
            trackFormFields: [],
            get totalWeight() {
                return this.trackAssessments.reduce((sum, item) => sum + (parseFloat(item.weight) || 0), 0);
            },
            loadingMapping: false,
            mappingTrackId: null,
            detailData: null,
            table: null,

            init() {
                this.initDataTable();
                this.initSelect2();

                const mappingModalEl = document.getElementById('mappingModal');
                if (mappingModalEl) {
                    this.mappingModal = new bootstrap.Modal(mappingModalEl);
                    mappingModalEl.addEventListener('hide.bs.modal', () => document.activeElement?.blur());
                }

                // Global function for onclick actions
                window.editTrack = (id) => this.editTrack(id);
                window.deleteTrack = (id) => this.deleteTrack(id);
                window.manageMappings = (id) => this.manageMappings(id);
            },

            initSelect2() {
                $('.select2-master').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Pilih Jalur --',
                    width: '100%'
                }).on('change', (e) => {
                    this.formData.master_track_type_id = e.target.value;
                });
            },

            initDataTable() {
                if ($.fn.DataTable.isDataTable('#trackTable')) {
                    this.table = $('#trackTable').DataTable();
                    return;
                }

                this.table = $('#trackTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: `{{ route('admin.spmb.configurations.tracks.data', ':config_id') }}`.replace(':config_id', this.configurationId),
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'track_name', name: 'trackType.name' },
                        { data: 'quota', name: 'quota' },
                        { data: 'registration_fee', name: 'registration_fee' },
                        { data: 'status', name: 'status', className: 'text-center' },
                    ],
                    language: {
                        "sEmptyTable": "Tidak ada data yang tersedia pada tabel ini",
                        "sProcessing": "Sedang memproses...",
                        "sLengthMenu": "Tampilkan _MENU_ entri",
                        "sZeroRecords": "Tidak ditemukan data yang sesuai",
                        "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sSearch": "Cari:",
                        "oPaginate": {
                            "sFirst": "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext": "Selanjutnya",
                            "sLast": "Terakhir"
                        }
                    }
                });
            },

            refreshTable() {
                this.table.ajax.reload(null, false);
            },

            resetForm() {
                this.editId = null;
                this.formData = {
                    master_track_type_id: '',
                    quota: '',
                    registration_fee: 0,
                    payment_mode: 'PRE_PAYMENT',
                    allow_carryover: false,
                    announcement_date: '',
                    status: 'active'
                };
                // Reset Select2
                $('.select2-master').val('').trigger('change');
            },

            async editTrack(id) {
                try {
                    const url = `{{ route('admin.spmb.configurations.tracks.show', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', id);
                    const response = await axios.get(url);
                    const data = response.data;

                    this.formData = {
                        master_track_type_id: data.master_track_type_id,
                        quota: data.quota,
                        registration_fee: data.registration_fee,
                        payment_mode: data.payment_mode,
                        allow_carryover: data.allow_carryover,
                        announcement_date: data.announcement_date ? data.announcement_date.substring(0, 16) : '',
                        status: data.status
                    };

                    this.editId = id;

                    // Update Select2 value
                    $('.select2-master').val(data.master_track_type_id).trigger('change');

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (error) {
                    Swal.fire('Gagal', 'Gagal memuat data jalur', 'error');
                }
            },

            async submitForm() {
                this.loading = true;

                try {
                    let url, method;

                    if (this.editId) {
                        url = `{{ route('admin.spmb.configurations.tracks.update', ['configuration' => ':config_id', 'track' => ':id']) }}`
                            .replace(':config_id', this.configurationId)
                            .replace(':id', this.editId);
                        method = 'put';
                    } else {
                        url = `{{ route('admin.spmb.configurations.tracks.store', ':config_id') }}`
                            .replace(':config_id', this.configurationId);
                        method = 'post';
                    }

                    const response = await axios[method](url, this.formData);

                    toastr.success(response.data.message);
                    this.resetForm();
                    this.refreshTable();
                } catch (error) {
                    let message = 'Terjadi kesalahan sistem';
                    if (error.response?.status === 422) {
                        // Ambil pesan error pertama dari Laravel validation
                        const errors = error.response.data.errors;
                        message = Object.values(errors).flat()[0] || error.response.data.message;
                    } else {
                        message = error.response?.data?.message || error.message;
                    }
                    toastr.error(message);
                } finally {
                    this.loading = false;
                }
            },

            async deleteTrack(id) {
                const result = await Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Jalur yang dihapus akan menghilangkan semua data pendaftar terkait!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                });

                if (result.isConfirmed) {
                    try {
                        const url = `{{ route('admin.spmb.configurations.tracks.destroy', ['configuration' => ':config_id', 'track' => ':id']) }}`
                            .replace(':config_id', this.configurationId)
                            .replace(':id', id);

                        const response = await axios.delete(url);
                        toastr.success(response.data.message);
                        this.refreshTable();
                    } catch (error) {
                        toastr.error('Gagal menghapus jalur.');
                    }
                }
            },

            async manageMappings(id) {
                this.mappingTrackId = id;
                this.trackFees = [];
                this.trackAssessments = []; // Reset assessments too
                this.trackFormFields = []; // Reset form fields
                this.detailData = null;

                // Reset to first tab (Fees)
                const firstTabEl = document.querySelector('#fee-tab');
                if (firstTabEl) {
                    bootstrap.Tab.getOrCreateInstance(firstTabEl).show();
                }
                
                try {
                    // Fetch master data if not loaded yet
                    if (this.masterFees.length === 0) {
                        const urlMaster = `{{ route('admin.spmb.configurations.tracks.fees.master', ['configuration' => ':config_id', 'track' => ':id']) }}`
                            .replace(':config_id', this.configurationId)
                            .replace(':id', id);
                        const resMaster = await axios.get(urlMaster);
                        this.masterFees = resMaster.data;
                    }

                    if (this.masterAssessments.length === 0) {
                        const urlMasterAssess = `{{ route('admin.spmb.configurations.tracks.assessments.master', ['configuration' => ':config_id', 'track' => ':id']) }}`
                            .replace(':config_id', this.configurationId)
                            .replace(':id', id);
                        const resMasterAssess = await axios.get(urlMasterAssess);
                        this.masterAssessments = resMasterAssess.data;
                    }

                    // Fetch track detail, fees and assessments
                    const urlTrack = `{{ route('admin.spmb.configurations.tracks.show', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', id);
                    const resTrack = await axios.get(urlTrack);
                    
                    this.detailData = {
                        track_name: resTrack.data.track_type?.name || resTrack.data.trackType?.name || 'Jalur'
                    };

                    const urlFees = `{{ route('admin.spmb.configurations.tracks.fees.index', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', id);
                    const resFees = await axios.get(urlFees);

                    const urlAssess = `{{ route('admin.spmb.configurations.tracks.assessments.index', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', id);
                    const resAssess = await axios.get(urlAssess);

                    const urlFields = `{{ route('admin.spmb.configurations.tracks.form-fields.index', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', id);
                    const resFields = await axios.get(urlFields);

                    // Await DOM update for master options before binding
                    await this.$nextTick();

                    // Show modal first so DOM is visible
                    this.mappingModal.show();
                    await this.$nextTick();

                    // Map fetched fees to UI structure (String cast for select binding)
                    this.trackFees = resFees.data.map(fee => ({
                        master_fee_component_id: String(fee.master_fee_component_id),
                        amount: parseFloat(fee.amount),
                        category: fee.category,
                        display_order: fee.display_order
                    }));

                    // Map fetched assessments to UI structure
                    this.trackAssessments = resAssess.data.map(assess => ({
                        master_assessment_type_id: String(assess.master_assessment_type_id),
                        weight: parseFloat(assess.weight),
                        passing_score: assess.passing_score ? parseFloat(assess.passing_score) : null,
                        display_order: assess.display_order
                    }));

                    // Map fetched form fields to UI structure
                    this.trackFormFields = resFields.data.map(field => {
                        let optionsText = '';
                        if (['select', 'radio', 'checkbox'].includes(field.field_type) && field.field_options) {
                            optionsText = Array.isArray(field.field_options) 
                                ? field.field_options.join(', ') 
                                : field.field_options;
                        }
                        return {
                            field_group: field.field_group || 'data_pribadi',
                            field_name: field.field_name,
                            field_label: field.field_label,
                            field_type: field.field_type,
                            field_options_text: optionsText,
                            field_options: field.field_options,
                            file_types: field.file_types || '',
                            max_file_size: field.max_file_size || null,
                            is_required: field.is_required,
                            display_order: field.display_order
                        };
                    });
                } catch (error) {
                    toastr.error('Gagal memuat data mapping persyaratan.');
                }
            },

            addFeeRow() {
                this.trackFees.push({
                    master_fee_component_id: '',
                    amount: 0,
                    category: 'registration',
                    display_order: this.trackFees.length + 1
                });
            },

            removeFeeRow(index) {
                this.trackFees.splice(index, 1);
            },

            async saveFees() {
                this.loadingMapping = true;
                try {
                    const url = `{{ route('admin.spmb.configurations.tracks.fees.sync', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', this.mappingTrackId);

                    const payload = {
                        fees: this.trackFees
                    };

                    const response = await axios.post(url, payload);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        target: document.getElementById('mappingModal')
                    });
                } catch (error) {
                    let message = 'Terjadi kesalahan sistem';
                    if (error.response?.status === 422) {
                        message = error.response.data.message || 'Validasi Gagal';
                    }
                    toastr.error(message);
                } finally {
                    this.loadingMapping = false;
                }
            },

            addAssessmentRow() {
                this.trackAssessments.push({
                    master_assessment_type_id: '',
                    weight: 0,
                    passing_score: null,
                    display_order: this.trackAssessments.length + 1
                });
            },

            removeAssessmentRow(index) {
                this.trackAssessments.splice(index, 1);
            },

            async saveAssessments() {
                if (this.totalWeight !== 100 && this.trackAssessments.length > 0) {
                    const confirm = await Swal.fire({
                        title: 'Konfirmasi Bobot',
                        text: `Total bobot saat ini adalah ${this.totalWeight}%. Apakah Anda yakin ingin menyimpan? Idealnya total bobot adalah 100%.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                        target: document.getElementById('mappingModal')
                    });

                    if (!confirm.isConfirmed) return;
                }

                this.loadingMapping = true;
                try {
                    const url = `{{ route('admin.spmb.configurations.tracks.assessments.sync', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', this.mappingTrackId);

                    const payload = {
                        assessments: this.trackAssessments
                    };

                    const response = await axios.post(url, payload);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        target: document.getElementById('mappingModal')
                    });
                } catch (error) {
                    let message = 'Terjadi kesalahan sistem';
                    if (error.response?.status === 422) {
                        message = error.response.data.message || 'Validasi Gagal';
                    }
                    toastr.error(message);
                } finally {
                    this.loadingMapping = false;
                }
            },

            addFormFieldRow() {
                this.trackFormFields.push({
                    field_group: 'data_pribadi',
                    field_name: '',
                    field_label: '',
                    field_type: 'text',
                    field_options_text: '',
                    field_options: null,
                    file_types: '',
                    max_file_size: null,
                    is_required: true,
                    display_order: this.trackFormFields.length + 1
                });
            },

            removeFormFieldRow(index) {
                this.trackFormFields.splice(index, 1);
            },

            generateSlug(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '_')           // Replace spaces with -
                    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                    .replace(/\-\-+/g, '_')         // Replace multiple - with single -
                    .replace(/^-+/, '')             // Trim - from start of text
                    .replace(/-+$/, '');            // Trim - from end of text
            },

            handleFieldTypeChange(field) {
                if (!['select', 'radio', 'checkbox'].includes(field.field_type)) {
                    field.field_options_text = '';
                    field.field_options = null;
                }
                if (field.field_type !== 'file') {
                    field.file_types = '';
                    field.max_file_size = null;
                }
            },

            async saveFormFields() {
                this.loadingMapping = true;
                try {
                    const url = `{{ route('admin.spmb.configurations.tracks.form-fields.sync', ['configuration' => ':config_id', 'track' => ':id']) }}`
                        .replace(':config_id', this.configurationId)
                        .replace(':id', this.mappingTrackId);

                    // Process options_text to array for dropdowns
                    const processedFields = this.trackFormFields.map(field => {
                        let options = null;
                        if (['select', 'radio', 'checkbox'].includes(field.field_type) && field.field_options_text) {
                            options = field.field_options_text.split(',').map(opt => opt.trim()).filter(opt => opt);
                        }
                        return {
                            ...field,
                            field_options: options
                        };
                    });

                    const payload = {
                        form_fields: processedFields
                    };

                    const response = await axios.post(url, payload);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        target: document.getElementById('mappingModal')
                    });
                } catch (error) {
                    let message = 'Terjadi kesalahan sistem';
                    if (error.response?.status === 422) {
                        message = error.response.data.message || 'Validasi Gagal';
                    }
                    toastr.error(message);
                } finally {
                    this.loadingMapping = false;
                }
            }
        }
    }
</script>