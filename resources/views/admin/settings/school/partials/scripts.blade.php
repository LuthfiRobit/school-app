@push('js')
<script>
    function schoolIdentityApp() {
        return {
            formData: {
                school_name: '{{ $school->school_name ?? "" }}',
                npsn: '{{ $school->npsn ?? "" }}',
                education_level: '{{ $school->education_level->value ?? "" }}',
                school_status: '{{ $school->school_status->value ?? "Negeri" }}',
                ownership_status: '{{ $school->ownership_status->value ?? "" }}',
                establishment_sk: '{{ $school->establishment_sk ?? "" }}',
                establishment_date: '{{ isset($school->establishment_date) ? $school->establishment_date->format("Y-m-d") : "" }}',
                operational_sk: '{{ $school->operational_sk ?? "" }}',
                tax_id: '{{ $school->tax_id ?? "" }}',
                accreditation: '{{ $school->accreditation->value ?? "" }}',
                accreditation_expiry_date: '{{ isset($school->accreditation_expiry_date) ? $school->accreditation_expiry_date->format("Y-m-d") : "" }}',
                address: '{{ $school->address ?? "" }}',
                latitude: '{{ $school->latitude ?? "" }}',
                longitude: '{{ $school->longitude ?? "" }}',
                whatsapp: '{{ $school->whatsapp ?? "" }}',
                phone: '{{ $school->phone ?? "" }}',
                email: '{{ $school->email ?? "" }}',
                website: '{{ $school->website ?? "" }}',
                headmaster_name: '{{ $school->headmaster_name ?? "" }}',
                headmaster_nip: '{{ $school->headmaster_nip ?? "" }}',
                treasurer_name: '{{ $school->treasurer_name ?? "" }}',
                treasurer_nip: '{{ $school->treasurer_nip ?? "" }}',
                operator_name: '{{ $school->operator_name ?? "" }}',
                operator_nip: '{{ $school->operator_nip ?? "" }}',
                logo_url: '{{ ($school && $school->logo) ? asset("storage/" . $school->logo) : "" }}',
                stamp_url: '{{ ($school && $school->stamp) ? asset("storage/" . $school->stamp) : "" }}',
                profile_image_url: '{{ ($school && $school->profile_image) ? asset("storage/" . $school->profile_image) : "" }}',
            },
            files: { logo: null, stamp: null, profile_image: null },
            previews: { logo: null, stamp: null, profile_image: null },
            loading: false,

            initSelect2(el, field) {
                const self = this;
                $(el).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(el).data('placeholder'),
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.formData[field] = $(this).val();
                });

                if (this.formData[field]) {
                    $(el).val(this.formData[field]).trigger('change.select2');
                }
            },

            handleFileUpload(event, type) {
                const file = event.target.files[0];
                if (file) {
                    this.files[type] = file;
                    this.previews[type] = URL.createObjectURL(file);
                }
            },

            async submitForm() {
                const result = await Swal.fire({
                    title: 'Simpan Perubahan?',
                    text: "Seluruh data identitas lembaga akan diperbarui.",
                    icon: 'question', showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!', cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) return;

                this.loading = true;
                const formData = new FormData();
                
                for (let key in this.formData) {
                    if (!key.endsWith('_url')) {
                        formData.append(key, this.formData[key] || '');
                    }
                }
                
                if (this.files.logo) formData.append('logo', this.files.logo);
                if (this.files.stamp) formData.append('stamp', this.files.stamp);
                if (this.files.profile_image) formData.append('profile_image', this.files.profile_image);

                try {
                    const response = await fetch("{{ route('admin.settings.school.update') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    });

                    const data = await response.json();
                    if (response.ok) {
                        toastr.success(data.message);
                        if (data.data.logo) { this.formData.logo_url = `{{ asset('storage') }}/${data.data.logo}`; this.previews.logo = null; }
                        if (data.data.stamp) { this.formData.stamp_url = `{{ asset('storage') }}/${data.data.stamp}`; this.previews.stamp = null; }
                        if (data.data.profile_image) { this.formData.profile_image_url = `{{ asset('storage') }}/${data.data.profile_image}`; this.previews.profile_image = null; }
                    } else {
                        toastr.error(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    toastr.error('Gagal terhubung ke server');
                } finally { this.loading = false; }
            }
        };
    }
</script>
@endpush
