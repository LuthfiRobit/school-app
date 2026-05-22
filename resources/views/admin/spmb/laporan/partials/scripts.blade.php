<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('laporanSpmb', () => ({
            filter: {
                academic_year_id: '{{ $academicYears->first()?->id ?? '' }}',
                spmb_track_id: '',
                status: ''
            },
            loading: true,
            exportingExcel: false,
            exportingPdf: false,
            kpi: {
                total_pendaftar: 0,
                total_lulus: 0,
                total_siswa_tetap: 0,
                total_penerimaan: 0
            },
            tracks: [],
            statusDistribution: [],
            jalurChart: null,
            statusChart: null,

            init() {
                this.loadData();
            },

            /**
             * Inisialisasi Select2 dengan tema Bootstrap 5 dan sinkronkan dengan Alpine state.
             * Sesuai pola laravel-frontend-standard SKILL:
             *   - Gunakan 'change' event untuk update filter state
             *   - Gunakan 'change.select2' untuk set nilai awal tanpa trigger loadData
             */
            initSelect2(el, field) {
                const self = this;
                $(el).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(el).data('placeholder'),
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.filter[field] = $(this).val() || '';
                    self.loadData();
                });

                // Set nilai awal dari Alpine state ke Select2 (tanpa trigger loadData)
                if (this.filter[field]) {
                    $(el).val(this.filter[field]).trigger('change.select2');
                }
            },

            loadData() {
                this.loading = true;

                axios.get('{{ route("admin.spmb.laporan.summary") }}', {
                    params: this.filter
                })
                .then(response => {
                    const data = response.data.data;
                    this.kpi = data.kpi;
                    this.tracks = data.tracks;
                    this.statusDistribution = data.status_distribution;

                    this.loading = false;

                    // Render charts setelah DOM diupdate Alpine
                    this.$nextTick(() => {
                        this.renderJalurChart();
                        this.renderStatusChart();
                    });
                })
                .catch(error => {
                    console.error('Error loading report data:', error);
                    toastr.error('Gagal memuat data laporan.');
                    this.loading = false;
                });
            },

            renderJalurChart() {
                const ctx = document.getElementById('laporanJalurChart');
                if (!ctx) return;

                if (this.jalurChart) {
                    this.jalurChart.destroy();
                }

                const labels     = this.tracks.map(t => t.name);
                const dataActive = this.tracks.map(t => t.total_active);
                const dataPassed = this.tracks.map(t => t.total_passed);

                this.jalurChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Pendaftar',
                                data: dataActive,
                                backgroundColor: '#4680FF',
                                borderRadius: 4,
                            },
                            {
                                label: 'Lulus Seleksi',
                                data: dataPassed,
                                backgroundColor: '#2CA87F',
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            },

            renderStatusChart() {
                const ctx = document.getElementById('laporanStatusChart');
                if (!ctx) return;

                if (this.statusChart) {
                    this.statusChart.destroy();
                }

                const labels = this.statusDistribution.map(s => s.label);
                const data   = this.statusDistribution.map(s => s.total);
                const colors = this.statusDistribution.map(s => s.color);

                this.statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 20,
                                    boxWidth: 12
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            },

            async exportData(type) {
                if (type === 'excel') this.exportingExcel = true;
                else if (type === 'pdf') this.exportingPdf = true;

                const queryParams = new URLSearchParams(this.filter).toString();
                const url = type === 'excel' 
                    ? `{{ route('admin.spmb.laporan.export.excel') }}?${queryParams}` 
                    : `{{ route('admin.spmb.laporan.export.pdf') }}?${queryParams}`;

                try {
                    const response = await axios.get(url, { responseType: 'blob' });
                    
                    // Buat blob object dan URL
                    const blob = new Blob([response.data], { type: response.headers['content-type'] });
                    const downloadUrl = window.URL.createObjectURL(blob);
                    
                    // Coba extract filename dari content-disposition header jika ada
                    let filename = type === 'excel' ? 'Laporan_SPMB.xlsx' : 'Ringkasan_Laporan_SPMB.pdf';
                    const disposition = response.headers['content-disposition'];
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        const matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) { 
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    // Trigger trigger download via element <a>
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.setAttribute('download', filename);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(downloadUrl);
                    
                    toastr.success('File berhasil diunduh.');
                } catch (error) {
                    console.error('Export failed:', error);
                    toastr.error('Terjadi kesalahan saat mengunduh file.');
                } finally {
                    if (type === 'excel') this.exportingExcel = false;
                    else if (type === 'pdf') this.exportingPdf = false;
                }
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num || 0);
            },

            formatCurrency(num) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num || 0);
            }
        }));
    });
</script>
