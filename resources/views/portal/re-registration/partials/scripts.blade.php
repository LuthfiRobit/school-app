<script>
  function copyToClipboard(text, bankName) {
    navigator.clipboard.writeText(text).then(function() {
      toastr.success('Nomor rekening ' + bankName + ' berhasil disalin!');
    }, function(err) {
      toastr.error('Gagal menyalin nomor rekening.');
    });
  }

  function confirmFinalization(event, name, totalPaid) {
    event.preventDefault();
    const form = document.getElementById('finalize-form');
    
    Swal.fire({
      title: 'Konfirmasi Finalisasi',
      html: `Apakah Anda yakin ingin memfinalisasi daftar ulang Anda?<br><br>
             <strong>Nama Siswa:</strong> ${name}<br>
             <strong>Total Uang Pangkal Lunas:</strong> Rp ${totalPaid}<br><br>
             <em>Setelah difinalisasi, Anda resmi terdaftar sebagai Siswa Tetap. Anda tidak dapat mengunggah bukti pembayaran cicilan baru lagi.</em>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Ya, Finalisasi Sekarang!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
  }

  function uploadPaymentForm() {
    return {
      dragOver: false,
      fileSelected: false,
      fileName: '',
      fileSize: '',
      filePreview: '',
      isImage: false,
      formattedAmount: '',
      rawAmount: 0,
      loading: false,

      handleFileSelect(e) {
        const file = e.target.files[0];
        this.processFile(file);
      },

      handleFileDrop(e) {
        const file = e.dataTransfer.files[0];
        if (file) {
          this.$refs.fileInput.files = e.dataTransfer.files;
          this.processFile(file);
        }
      },

      processFile(file) {
        if (!file) return;

        // Size check (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
          toastr.error('Ukuran berkas maksimal adalah 2MB.');
          this.resetFile();
          return;
        }

        // MIME type validation
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
          toastr.error('Tipe berkas tidak didukung. Harap pilih gambar JPEG/PNG atau berkas PDF.');
          this.resetFile();
          return;
        }

        this.fileName = file.name;
        this.fileSize = this.formatBytes(file.size);
        this.fileSelected = true;

        if (file.type.startsWith('image/')) {
          this.isImage = true;
          const reader = new FileReader();
          reader.onload = (e) => {
            this.filePreview = e.target.result;
          };
          reader.readAsDataURL(file);
        } else {
          this.isImage = false;
          this.filePreview = '';
        }
      },

      resetFile() {
        this.$refs.fileInput.value = '';
        this.fileSelected = false;
        this.fileName = '';
        this.fileSize = '';
        this.filePreview = '';
        this.isImage = false;
      },

      formatBytes(bytes, decimals = 2) {
        if (!bytes) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
      },

      formatInput(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value === '') {
          this.rawAmount = 0;
          this.formattedAmount = '';
          return;
        }
        
        this.rawAmount = parseInt(value, 10);
        this.formattedAmount = this.rawAmount.toLocaleString('id-ID');
      }
    };
  }

  // Initialize tooltips
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>
