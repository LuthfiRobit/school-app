---
name: laravel-frontend-standard
description: Standard for building modular, reactive, and premium UI in Laravel using Alpine.js, Select2 (BS5 Theme), SweetAlert2, Toastr, and DataTables.
---

# Laravel Frontend Standard (School App)

Panduan ini mendefinisikan standar pengembangan antarmuka (UI/UX) untuk memastikan konsistensi, modularitas, dan performa di seluruh aplikasi.

## 1. Arsitektur Folder & Modularitas (Blade)
Setiap modul view harus dipecah ke dalam folder `partials/` untuk menghindari file tunggal yang terlalu panjang.

**Struktur Standar:**
- `index.blade.php` (Entry point & Layout wrapper)
- `partials/form.blade.php` (Input/Edit data)
- `partials/table.blade.php` (Listing data/DataTables)
- `partials/modal.blade.php` (Detail/Pop-up info)
- `partials/scripts.blade.php` (Logika Alpine.js & Inisialisasi JS)

## 2. State Management (Alpine.js)
Gunakan Alpine.js untuk mengelola state form dan interaksi reaktif tanpa reload halaman.

- **Centralized Data**: Definisikan `formData` untuk menampung seluruh nilai input.
- **Loading State**: Gunakan properti `loading: false` untuk mengontrol tombol submit dan spinner.
- **Null-Safety**: Selalu lakukan pengecekan null/isset pada data awal dari database (khususnya untuk format tanggal).

```javascript
function moduleApp() {
    return {
        formData: { ... },
        loading: false,
        // ... methods
    }
}
```

## 3. Integrasi Select2 + Alpine.js
Untuk menggunakan Select2 dengan tema Bootstrap 5 dan tetap sinkron dengan Alpine.js, gunakan pola `x-init`.

**Blade Implementation:**
```html
<select class="form-select select2-alpine" 
    x-init="initSelect2($el, 'field_name')"
    data-placeholder="Pilih Opsi">
    <!-- options -->
</select>
```

**JavaScript Implementation (dalam `scripts.blade.php`):**
```javascript
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

    // Sinkronkan nilai awal
    if (this.formData[field]) {
        $(el).val(this.formData[field]).trigger('change.select2');
    }
}
```

## 4. Feedback & Notifikasi
- **Konfirmasi**: Gunakan **SweetAlert2** untuk setiap aksi destruktif (Hapus) atau perubahan status kritis (Toggle Aktif).
- **Notifikasi**: Gunakan **Toastr** untuk umpan balik instan (Sukses/Gagal) setelah operasi AJAX.
- **Placeholder**: Setiap input wajib memiliki `placeholder` yang deskriptif sebagai panduan bagi pengguna.

## 5. DataTables (Server-side)
- Gunakan `admin.layouts.app` yang sudah menyertakan dependensi DataTables.
- Tempatkan inisialisasi tabel di dalam method `initTable()` pada Alpine app.
- Pastikan kolom aksi (Edit/Detail/Delete) ditempatkan secara konsisten (disarankan di sebelah kiri setelah nomor).

## 6. Audit Logging (Backend Sync)
Pastikan model yang bersangkutan telah menggunakan `booted()` untuk mengisi `created_by` dan `updated_by` secara otomatis, sehingga frontend tidak perlu mengirimkan data audit ini secara manual.

---
**Catatan**: Selalu utamakan estetika premium dengan menggunakan komponen dari template **Able Pro** dan ikon dari **Tabler Icons**.
