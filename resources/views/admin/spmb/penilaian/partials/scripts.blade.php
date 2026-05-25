<script>
    function assessmentApp() {
        return {
            loading: false,
            table: null,
            selectedIds: [],
            filter: {
                spmb_track_id: '',
                status: ''
            },

            // State untuk modal nilai (inline edit)
            nilaiModal: {
                enrollment_id: null,
                enrollment_number: '',
                applicant_name: '',
                status_label: '',
                status_badge: '',
                track_name: '',
                academic_year: '',
                components: [],
                weighted_score: 0,
                all_assessed: false,
                assessed_count: 0,
                total_count: 0,
                progress_pct: 0,
                showWarning: false
            },

            // State untuk modal kelulusan individu
            decisionForm: {
                enrollment_id: null,
                enrollment_number: '',
                applicant_name: '',
                status_label: '',
                status_badge: '',
                weighted_score: 0,
                all_assessed: false,
                status: '',
                waitlist_order: null,
                reason: '',
                waitlist_info: {
                    current_count: 0,
                    max_order: 0,
                    next_suggested: 1,
                    list: []
                }
            },

            // State untuk modal kelulusan massal
            bulkDecisionForm: {
                status: '',
                reason: ''
            },

            // =====================================================================
            // INITIALIZATION
            // =====================================================================
            init() {
                this.initDataTable();
                this.setupEventListeners();
            },

            initDataTable() {
                if ($.fn.DataTable.isDataTable('#assessmentTable')) {
                    $('#assessmentTable').DataTable().destroy();
                }
                const self = this;
                this.table = $('#assessmentTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.spmb.penilaian.data') }}",
                        data: function (d) {
                            d.spmb_track_id = self.filter.spmb_track_id;
                            d.status        = self.filter.status;
                        }
                    },
                    columns: [
                        { data: 'checkbox',              name: 'checkbox',       orderable: false, searchable: false },
                        { data: 'DT_RowIndex',           name: 'DT_RowIndex',    orderable: false, searchable: false },
                        { data: 'action',                name: 'action',         orderable: false, searchable: false },
                        { data: 'enrollment_number',     name: 'enrollment_number' },
                        { data: 'full_name',             name: 'applicant.full_name' },
                        { data: 'track_name',            name: 'spmbTrack.trackType.name' },
                        { data: 'academic_year',         name: 'spmbTrack.spmbConfiguration.academicYear.name' },
                        { data: 'assessment_progress',   name: 'assessment_progress', orderable: false },
                        { data: 'score_formatted',       name: 'score_formatted', orderable: false, className: 'text-center' },
                        { data: 'status_badge',          name: 'status',         orderable: false },
                        { data: 'created_at_formatted',  name: 'created_at' }
                    ],
                    language: {
                        "sEmptyTable":   "Tidak ada pendaftar yang siap dinilai",
                        "sProcessing":   "Sedang memproses...",
                        "sLengthMenu":   "Tampilkan _MENU_ entri",
                        "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                        "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sSearch":       "Cari:",
                        "oPaginate": {
                            "sFirst":    "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext":     "Selanjutnya",
                            "sLast":     "Terakhir"
                        }
                    }
                });

                // Reset checkbox state on every draw
                this.table.on('draw', () => {
                    this.selectedIds = [];
                    $('#checkAll').prop('checked', false);
                });
            },

            // =====================================================================
            // SELECT2 ALPINE INTEGRATION (Frontend Standard)
            // =====================================================================
            initSelect2(el, field) {
                const self = this;
                $(el).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(el).data('placeholder'),
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.filter[field] = $(this).val() || '';
                    self.refreshTable();
                });

                if (this.filter[field]) {
                    $(el).val(this.filter[field]).trigger('change.select2');
                }
            },

            // =====================================================================
            // EVENT LISTENERS
            // =====================================================================
            setupEventListeners() {
                const self = this;
                const $table = $('#assessmentTable');

                // Klik tombol Detail Nilai
                $table.on('click', '.btn-detail', function() {
                    const id = $(this).data('id');
                    self.openNilaiModal(id);
                });

                // Klik tombol Tetapkan Kelulusan
                $table.on('click', '.btn-decision', function() {
                    const id = $(this).data('id');
                    self.openDecisionModal(id);
                });

                // Checkbox individual
                $table.on('change', '.check-item', function() {
                    const id = $(this).val();
                    if ($(this).is(':checked')) {
                        if (!self.selectedIds.includes(id)) {
                            self.selectedIds.push(id);
                        }
                    } else {
                        self.selectedIds = self.selectedIds.filter(item => item !== id);
                    }

                    const allChecked = $('.check-item:checked').length === $('.check-item').length && $('.check-item').length > 0;
                    $('#checkAll').prop('checked', allChecked);
                });

                // Bersihkan backdrop modal saat ditutup
                $('#nilaiModal, #kelulusanModal, #bulkKelulusanModal').on('hidden.bs.modal', function () {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');
                });
            },

            // =====================================================================
            // HELPERS
            // =====================================================================
            toggleAll(e) {
                const isChecked = e.target.checked;
                this.selectedIds = [];
                $('.check-item').prop('checked', isChecked);

                if (isChecked) {
                    $('.check-item').each((i, el) => {
                        this.selectedIds.push($(el).val());
                    });
                }
            },

            refreshTable() {
                this.table.ajax.reload();
            },

            // =====================================================================
            // MODAL NILAI — Inline Edit per Komponen
            // =====================================================================
            async openNilaiModal(enrollmentId) {
                try {
                    const response = await axios.get(
                        `{{ route('admin.spmb.penilaian.show', ':id') }}`.replace(':id', enrollmentId)
                    );
                    const data = response.data;

                    const assessedCount = data.components.filter(c => c.score !== null).length;
                    const totalCount    = data.components.length;
                    const progressPct   = totalCount > 0 ? Math.round((assessedCount / totalCount) * 100) : 0;

                    this.nilaiModal = {
                        enrollment_id:     data.enrollment.id,
                        enrollment_number: data.enrollment.enrollment_number || '-',
                        applicant_name:    data.applicant.full_name || '-',
                        status_label:      data.enrollment.status_label,
                        status_badge:      data.enrollment.status_badge,
                        track_name:        data.track.name || '-',
                        academic_year:     data.track.academic_year || '-',
                        components:        data.components,
                        weighted_score:    data.weighted_score,
                        all_assessed:      data.all_assessed,
                        assessed_count:    assessedCount,
                        total_count:       totalCount,
                        progress_pct:      progressPct,
                        showWarning:       false
                    };

                    const modal = new bootstrap.Modal(this.$refs.nilaiModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat memuat data penilaian.', 'error');
                }
            },

            async saveScore(comp) {
                if (this.nilaiModal.enrollment_id === null) return;

                const confirmed = await Swal.fire({
                    title: 'Simpan Nilai?',
                    html: `Simpan nilai untuk komponen <strong>${comp.assessment_name}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-check me-1"></i> Ya, Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0d6efd',
                });

                if (!confirmed.isConfirmed) return;

                this.loading = true;
                try {
                    const response = await axios.post(
                        `{{ route('admin.spmb.penilaian.upsert', ':id') }}`.replace(':id', this.nilaiModal.enrollment_id),
                        {
                            spmb_track_assessment_id: comp.mapping_id,
                            score: comp.score,
                            grade: comp.grade,
                            notes: comp.notes
                        }
                    );

                    // Update local component state with response
                    const saved = response.data.assessment;
                    comp.score         = saved.score !== null ? parseFloat(saved.score) : null;
                    comp.grade         = saved.grade;
                    comp.assessed_at   = saved.assessed_at;
                    comp.assessor_name = '{{ auth()->user()?->name ?? "Admin" }}';

                    // Update weighted score & progress
                    this.nilaiModal.weighted_score = response.data.weighted_score;
                    this.nilaiModal.all_assessed   = response.data.all_assessed;
                    this.nilaiModal.assessed_count = this.nilaiModal.components.filter(c => c.score !== null).length;
                    this.nilaiModal.progress_pct   = this.nilaiModal.total_count > 0
                        ? Math.round((this.nilaiModal.assessed_count / this.nilaiModal.total_count) * 100)
                        : 0;

                    toastr.success(`Nilai untuk ${comp.assessment_name} berhasil disimpan!`, 'Berhasil');
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal Menyimpan', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // =====================================================================
            // MODAL KELULUSAN — Individual Decision
            // =====================================================================
            async openDecisionModal(enrollmentId) {
                try {
                    const response = await axios.get(
                        `{{ route('admin.spmb.penilaian.show', ':id') }}`.replace(':id', enrollmentId)
                    );
                    const data = response.data;

                    this.decisionForm = {
                        enrollment_id:     data.enrollment.id,
                        enrollment_number: data.enrollment.enrollment_number || '-',
                        applicant_name:    data.applicant.full_name || '-',
                        status_label:      data.enrollment.status_label,
                        status_badge:      data.enrollment.status_badge,
                        weighted_score:    data.weighted_score,
                        all_assessed:      data.all_assessed,
                        status:            ['passed', 'waiting_list', 'rejected'].includes(data.enrollment.status) ? data.enrollment.status : '',
                        waitlist_order:    data.enrollment.waitlist_order || null,
                        reason:            '',
                        waitlist_info:     data.waitlist_info || { current_count: 0, max_order: 0, next_suggested: 1, list: [] }
                    };

                    // Auto suggest waitlist order if they select waiting_list and don't have one set yet
                    if (this.decisionForm.status === 'waiting_list' && !this.decisionForm.waitlist_order) {
                        this.decisionForm.waitlist_order = this.decisionForm.waitlist_info.next_suggested;
                    }

                    const modal = new bootstrap.Modal(this.$refs.kelulusanModal);
                    modal.show();
                } catch (error) {
                    Swal.fire('Gagal', 'Tidak dapat memuat data pendaftar.', 'error');
                }
            },

            async submitDecision() {
                if (!this.decisionForm.status) return;

                const statusLabels = { passed: 'Lulus', waiting_list: 'Cadangan (Waiting List)', rejected: 'Tidak Lulus' };
                const confirmed = await Swal.fire({
                    title: 'Konfirmasi Penetapan Kelulusan',
                    html: `Anda akan menetapkan status <strong>${statusLabels[this.decisionForm.status]}</strong> untuk <strong>${this.decisionForm.applicant_name}</strong>.<br><br>Tindakan ini akan memperbarui status pendaftar secara permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-check me-1"></i> Ya, Tetapkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: this.decisionForm.status === 'passed' ? '#198754' : (this.decisionForm.status === 'rejected' ? '#dc3545' : '#ffc107'),
                });

                if (!confirmed.isConfirmed) return;

                this.loading = true;
                try {
                    const payload = {
                        status: this.decisionForm.status,
                        reason: this.decisionForm.reason
                    };
                    if (this.decisionForm.status === 'waiting_list') {
                        payload.waitlist_order = this.decisionForm.waitlist_order;
                    }

                    const response = await axios.post(
                        `{{ route('admin.spmb.penilaian.decision', ':id') }}`.replace(':id', this.decisionForm.enrollment_id),
                        payload
                    );

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Ditetapkan!',
                        text: response.data.message,
                        timer: 2500,
                        showConfirmButton: false
                    });

                    bootstrap.Modal.getInstance(this.$refs.kelulusanModal).hide();
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // =====================================================================
            // MODAL KELULUSAN MASSAL
            // =====================================================================
            openBulkDecisionModal() {
                this.bulkDecisionForm = { status: '', reason: '' };
                const modal = new bootstrap.Modal(this.$refs.bulkKelulusanModal);
                modal.show();
            },

            async submitBulkDecision() {
                if (!this.bulkDecisionForm.status) return;

                const statusLabels = { passed: 'Lulus', waiting_list: 'Cadangan', rejected: 'Tidak Lulus' };
                const confirmed = await Swal.fire({
                    title: 'Konfirmasi Penetapan Massal',
                    html: `Anda akan menetapkan status <strong>${statusLabels[this.bulkDecisionForm.status]}</strong> untuk <strong>${this.selectedIds.length} pendaftar</strong>.<br><br>Pendaftar yang tidak memenuhi syarat transisi akan dilewati secara otomatis.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-check me-1"></i> Ya, Terapkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ffc107',
                });

                if (!confirmed.isConfirmed) return;

                this.loading = true;
                try {
                    const response = await axios.post("{{ route('admin.spmb.penilaian.bulk-decision') }}", {
                        ids:    this.selectedIds,
                        status: this.bulkDecisionForm.status,
                        reason: this.bulkDecisionForm.reason
                    });

                    // Show partial success info if any
                    if (response.data.errors && response.data.errors.length > 0) {
                        const errorList = response.data.errors.map(e => `<li>${e}</li>`).join('');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sebagian Berhasil',
                            html: `<p>${response.data.message}</p><ul class="text-start text-danger small">${errorList}</ul>`,
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.data.message,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }

                    bootstrap.Modal.getInstance(this.$refs.bulkKelulusanModal).hide();
                    this.selectedIds = [];
                    $('#checkAll').prop('checked', false);
                    this.refreshTable();
                } catch (error) {
                    const msg = error.response?.data?.message || 'Terjadi kesalahan sistem';
                    Swal.fire('Gagal', msg, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // =====================================================================
            // FORMATTING HELPERS
            // =====================================================================
            getEnrollmentStatusLabel(status) {
                const labels = {
                    draft:                  'Draft',
                    registered:             'Terdaftar',
                    waiting_payment_reg:    'Menunggu Pembayaran Formulir',
                    verified_reg:           'Pembayaran Terverifikasi',
                    in_review:              'Sedang Dinilai',
                    passed:                 'Lulus Seleksi',
                    waiting_list:           'Cadangan (Waiting List)',
                    rejected:               'Tidak Lulus',
                    waiting_payment_final:  'Menunggu Pembayaran Daftar Ulang',
                    settled:                'Lunas Daftar Ulang',
                    permanent_student:      'Siswa Tetap'
                };
                return labels[status] || status;
            },

            getEnrollmentStatusBadge(status) {
                const badges = {
                    draft:                  'bg-light-secondary text-secondary',
                    registered:             'bg-light-info text-info',
                    waiting_payment_reg:    'bg-light-warning text-warning',
                    verified_reg:           'bg-light-success text-success',
                    in_review:              'bg-light-primary text-primary',
                    passed:                 'bg-light-success text-success',
                    waiting_list:           'bg-light-warning text-warning',
                    rejected:               'bg-light-danger text-danger',
                    waiting_payment_final:  'bg-light-warning text-warning',
                    settled:                'bg-light-success text-success',
                    permanent_student:      'bg-light-success text-success'
                };
                return badges[status] || 'bg-light';
            }
        };
    }
</script>
