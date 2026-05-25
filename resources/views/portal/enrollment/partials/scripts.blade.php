<script>
  function pendaftaranFormApp() {
    return {
      step: 1,
      saveStatus: 'saved',
      saveMessage: 'Draf Tersimpan',
      autoSaveTimer: null,
      fieldsData: @json($formData ?: (object)[]),

      init() {
        // Start auto-save every 30 seconds
        this.autoSaveTimer = setInterval(() => {
          this.saveDraft();
        }, 30000);
      },

      triggerAutoSave() {
        if (this.saveStatus === 'saved') {
          this.saveStatus = 'changed';
          this.saveMessage = 'Perubahan belum disimpan';
        }
      },

      saveDraft() {
        // Only save if status is not already saved (meaning there are changes)
        if (this.saveStatus === 'saved') return;

        this.saveStatus = 'saving';
        this.saveMessage = 'Menyimpan draf...';

        axios.post('{{ route("portal.enrollment.draft", $track->id) }}', {
          fields: this.fieldsData
        })
        .then(response => {
          if (response.data.success) {
            this.saveStatus = 'saved';
            const now = new Date();
            this.saveMessage = 'Draf Tersimpan (' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ')';
          } else {
            this.saveStatus = 'changed';
            this.saveMessage = 'Gagal menyimpan draf';
          }
        })
        .catch(error => {
          console.error(error);
          this.saveStatus = 'changed';
          this.saveMessage = 'Koneksi terputus';
        });
      },

      handleFileUpload(event, fieldId) {
        this.saveStatus = 'changed';
        this.saveMessage = 'Berkas dipilih (Belum dikirim)';
      },

      removeFile(fieldId) {
        this.saveStatus = 'changed';
        this.saveMessage = 'Berkas dihapus';
        this.fieldsData[fieldId] = '';
      },

      isStepValid(stepNum) {
        if (stepNum === 1) {
          return true; // Read-only step is always valid
        }
        if (stepNum === 2) {
          // Check that all required non-file fields are filled
          @foreach($formFields->where('field_type', '!=', 'file') as $field)
            @if($field->is_required)
              if (!this.fieldsData['{{ $field->id }}'] || this.fieldsData['{{ $field->id }}'].toString().trim() === '') {
                return false;
              }
            @endif
          @endforeach
          return true;
        }
        if (stepNum === 3) {
          return true;
        }
        return false;
      },

      nextStep() {
        if (this.step < 3 && this.isStepValid(this.step)) {
          if (this.saveStatus !== 'saved') {
            this.saveDraft();
          }
          this.step++;
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      },

      prevStep() {
        if (this.step > 1) {
          this.step--;
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      },

      submitForm() {
        // Trigger submit
        document.getElementById('enrollmentForm').submit();
      },

      // Progress bar calculations
      get totalRequiredFields() {
        let count = 0;
        @foreach($formFields as $field)
          @if($field->is_required)
            count++;
          @endif
        @endforeach
        return count;
      },

      get filledRequiredFields() {
        let filled = 0;
        
        // Count filled non-file required fields
        @foreach($formFields->where('field_type', '!=', 'file') as $field)
          @if($field->is_required)
            if (this.fieldsData['{{ $field->id }}'] && this.fieldsData['{{ $field->id }}'].toString().trim() !== '') {
              filled++;
            }
          @endif
        @endforeach

        // Count file fields
        @foreach($formFields->where('field_type', 'file') as $field)
          @if($field->is_required)
            if ('{{ !empty($formData[$field->id]) ? "true" : "false" }}' === 'true' || (this.fieldsData['{{ $field->id }}'] && this.fieldsData['{{ $field->id }}'] !== '')) {
              filled++;
            }
          @endif
        @endforeach

        return filled;
      },

      get filledPercentage() {
        if (this.totalRequiredFields === 0) return 100;
        return Math.round((this.filledRequiredFields / this.totalRequiredFields) * 100);
      },

      formatCurrency(amount) {
        if (amount <= 0) return 'Gratis';
        return 'Rp ' + amount.toLocaleString('id-ID');
      }
    }
  }
</script>
