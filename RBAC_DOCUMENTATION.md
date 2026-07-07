# LAPORAN AUDIT KEAMANAN DAN DOKUMENTASI TEKNIS
## Implementasi Sistem Role-Based Access Control (RBAC) pada Aplikasi Manajemen Sekolah

**Tanggal Audit:** 10 Juni 2026  
**Klasifikasi:** Internal / Dokumen Teknis  
**Peran:** Senior Security Architect & Technical Writer  
**Status Sistem:** Selesai Evaluasi  

---

### DAFTAR ISI
1. [Executive Summary](#1-executive-summary)
2. [Arsitektur & Komponen Sistem RBAC](#2-arsitektur--komponen-sistem-rbac)
   - [2.1 Skema Database & Migrasi](#21-skema-database--migrasi)
   - [2.2 Data Model & Enums](#22-data-model--enums)
   - [2.3 Arsitektur Lapisan Repositori](#23-arsitektur-lapisan-repositori)
   - [2.4 Manajemen & Sinkronisasi Permission Otomatis](#24-manajemen--sinkronisasi-permission-otomatis)
3. [Alur Autentikasi & Otorisasi Middleware](#3-alur-autentikasi--otorisasi-middleware)
4. [Temuan Audit Keamanan (Security Vulnerabilities)](#4-temuan-audit-keamanan-security-vulnerabilities)
   - [Temuan 1: Bypass Otorisasi pada Admin Dashboard (Kritis)](#temuan-1-bypass-otorisasi-pada-admin-dashboard-kritis)
   - [Temuan 2: Hardcoded Role Names pada Middleware EnsureIsApplicant (Sedang)](#temuan-2-hardcoded-role-names-pada-middleware-ensureisapplicant-sedang)
   - [Temuan 3: Kerentanan Reset Password dengan Nilai Statis (Sedang)](#temuan-3-kerentanan-reset-password-dengan-nilai-statis-sedang)
   - [Temuan 4: Risiko Degradasi Performa & Abuse Eksekusi Artisan via Web Request (Rendah)](#temuan-4-risiko-degradasi-performa--abuse-eksekusi-artisan-via-web-request-rendah)
   - [Temuan 5: Ketiadaan Validasi Privilege Escalation pada Update User/Role (Sedang)](#temuan-5-ketiadaan-validasi-privilege-escalation-pada-update-userrole-sedang)
5. [Rekomendasi Perbaikan & Panduan Remediasi](#5-rekomendasi-perbaikan--panduan-remediasi)

---

### 1. EXECUTIVE SUMMARY

Laporan ini menyajikan analisis mendalam mengenai implementasi sistem kontrol akses berbasis peran (*Role-Based Access Control* - RBAC) yang diterapkan pada Aplikasi Sistem Informasi Manajemen Sekolah. Evaluasi dilakukan dari perspektif arsitektur perangkat lunak dan arsitektur keamanan untuk mengukur tingkat ketahanan sistem terhadap ancaman eskalasi hak akses (*privilege escalation*) dan kebocoran data.

**Hasil Evaluasi Utama:**
* **Desain Modular:** Penerapan Repository Pattern memberikan pemisahan logika yang bersih (*separation of concerns*) antara pengontrol (*controller*) dan akses data.
* **Inovasi Skema (Scope):** Pengenalan konsep `scope` pada level peran (*role*) memberikan fleksibilitas tambahan untuk mengelompokkan peran berdasarkan domain fungsional sekolah (misal: `system`, `manajemen`, `pimpinan`, dll.).
* **Kerentanan Kritis:** Ditemukan satu celah keamanan berkategori **Kritis** di mana rute dashboard administrator utama `/admin/dashboard` **tidak diamankan dengan middleware peran/hak akses**. Hal ini memungkinkan pengguna dengan peran pendaftar (*Applicant*) untuk melihat data statistik internal sekolah dan pendaftar lainnya.
* **Code Smell & Kekakuan Otorisasi:** Adanya penulisan nama peran secara langsung (*hardcoded*) pada middleware portal pendaftar yang membatasi kedinamisan sistem RBAC.

---

### 2. ARSITEKTUR & KOMPONEN SISTEM RBAC

Sistem kontrol akses pada aplikasi ini dibangun di atas paket populer `spatie/laravel-permission` yang dimodifikasi untuk mendukung kebutuhan segmentasi akses multi-level menggunakan atribut `scope`.

#### 2.1 Skema Database & Migrasi
Pada berkas [2026_05_13_165547_create_permission_tables.php](file:///d:/laragon/www/school-app/database/migrations/2026_05_13_165547_create_permission_tables.php), tabel bawaan `roles` dari Spatie telah dimodifikasi dengan penambahan kolom `scope`:

```php
Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
    $table->id();
    ...
    $table->string('scope')->nullable(); // Penambahan atribut scope
    $table->string('name');
    $table->string('guard_name');
    $table->timestamps();
    ...
    $table->index('scope'); // Index untuk optimasi pencarian berdasarkan scope
});
```

Atribut `scope` ini bertujuan mengelompokkan berbagai peran ke dalam batas domain fungsional tertentu, mencegah pencampuran hak akses lintas domain organisasi sekolah.

#### 2.2 Data Model & Enums
Untuk menangani nilai `scope` secara terstruktur, aplikasi mendefinisikan Enum kelas [RoleScope.php](file:///d:/laragon/www/school-app/app/Enums/RoleScope.php):

```php
namespace App\Enums;

enum RoleScope: string
{
    case SYSTEM = 'system';
    case PIMPINAN = 'pimpinan';
    case MANAJEMEN = 'manajemen';
    case GURU = 'guru';
    case TENDIK = 'tendik';
    case SISWA = 'siswa';

    public function label(): string
    {
        return match($this) {
            self::SYSTEM => 'System',
            self::PIMPINAN => 'Pimpinan',
            ...
        };
    }
}
```

Model kustom [Role.php](file:///d:/laragon/www/school-app/app/Models/Role.php) memperluas model bawaan Spatie untuk menambahkan *casting* tipe data dan local scope query:

```php
namespace App\Models;

use App\Enums\RoleScope;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $casts = [
        'scope' => RoleScope::class, // Mengubah nilai database menjadi Enum instan
    ];

    public function scopeOfGroup($query, $group)
    {
        return $query->where('scope', $group);
    }
}
```

Pada model [User.php](file:///d:/laragon/www/school-app/app/Models/User.php), terdapat method pembantu untuk memeriksa cakupan akses pengguna:

```php
public function hasRoleScope($scopes): bool
{
    $scopes = is_array($scopes) ? $scopes : func_get_args();
    $userRoles = $this->roles;
    return $userRoles->whereIn('scope', $scopes)->isNotEmpty();
}
```

#### 2.3 Arsitektur Lapisan Repositori
Penanganan data diarahkan melalui repositori yang dideklarasikan oleh interface kontrak:
* [RoleRepositoryInterface.php](file:///d:/laragon/www/school-app/app/Repositories/Interfaces/RoleRepositoryInterface.php)
* [UserRepositoryInterface.php](file:///d:/laragon/www/school-app/app/Repositories/Interfaces/UserRepositoryInterface.php)

Implementasinya berada pada [RoleRepository.php](file:///d:/laragon/www/school-app/app/Repositories/RoleRepository.php) dan [UserRepository.php](file:///d:/laragon/www/school-app/app/Repositories/UserRepository.php).

Metode paling krusial di [RoleRepository.php](file:///d:/laragon/www/school-app/app/Repositories/RoleRepository.php) adalah `getGroupedPermissions()` yang mengelompokkan permission berdasarkan format penamaannya `{modul}.{submodul}.{aksi}`:

```php
public function getGroupedPermissions(): array
{
    $permissions = Permission::all();
    $grouped = [];

    foreach ($permissions as $permission) {
        $parts = explode('.', $permission->name);
        $count = count($parts);

        if ($count === 1) {
            $module = 'Umum';
            $resource = 'General';
            $action = ucfirst(str_replace('-', ' ', $parts[0]));
        } elseif ($count === 2) {
            $module = ucfirst(str_replace('-', ' ', $parts[0]));
            $resource = 'General';
            $action = ucfirst(str_replace('-', ' ', $parts[1]));
        } else {
            $module = ucfirst(str_replace('-', ' ', $parts[0]));
            $action = ucfirst(str_replace('-', ' ', end($parts)));
            $resourceParts = array_slice($parts, 1, -1);
            $resource = ucfirst(str_replace(['-', '.'], ' ', implode(' ', $resourceParts)));
        }

        $grouped[$module][$resource][] = [
            'id' => $permission->id,
            'name' => $action,
            'slug' => $permission->name
        ];
    }
    ...
    return $grouped;
}
```
Metode ini secara dinamis mengelompokkan izin akses agar antarmuka pengguna (UI) pengeditan peran di Blade menampilkan daftar hak akses dalam bentuk tabel hierarkis berdasarkan modul dan fungsinya secara rapi.

#### 2.4 Manajemen & Sinkronisasi Permission Otomatis
Alih-alih mendaftarkan izin akses (*permission*) secara manual di database atau melalui *seeder*, aplikasi ini menggunakan perintah konsol [SyncPermissions.php](file:///d:/laragon/www/school-app/app/Console/Commands/SyncPermissions.php) untuk memindai rute yang terdaftar pada [routes/web.php](file:///d:/laragon/www/school-app/routes/web.php).

Perintah `app:sync-permissions` memindai rute yang memiliki middleware bermotif `permission:{nama-permission}`:
```php
foreach ($routes as $route) {
    $middlewares = $route->gatherMiddleware();
    foreach ($middlewares as $middleware) {
        if (is_string($middleware) && str_contains($middleware, 'permission:')) {
            $segments = explode(':', $middleware);
            if (count($segments) > 1) {
                $permissionPart = $segments[1];
                $names = explode('|', $permissionPart);
                foreach ($names as $name) {
                    $cleanName = trim($name);
                    if ($cleanName) {
                        $permissionsFound[] = $cleanName;
                    }
                }
            }
        }
    }
}
```
Hasil pemindaian kemudian dicocokkan dengan database melalui `Permission::findOrCreate()`. Opsi `--prune` juga disediakan untuk menghapus izin akses yang sudah tidak digunakan lagi di dalam rute.

---

### 3. ALUR AUTENTIKASI & OTORISASI MIDDLEWARE

Aplikasi mendaftarkan beberapa middleware penanganan hak akses pada [bootstrap/app.php](file:///d:/laragon/www/school-app/bootstrap/app.php):

| Key Middleware | Kelas Implementasi | Deskripsi Fungsi |
| :--- | :--- | :--- |
| `permission` | `Spatie\Permission\Middleware\PermissionMiddleware` | Membatasi rute berdasarkan izin spesifik pengguna (misal: `settings.user.view`). |
| `role` | `Spatie\Permission\Middleware\RoleMiddleware` | Membatasi rute berdasarkan nama peran pengguna secara eksplisit. |
| `scope` | [CheckRoleScope.php](file:///d:/laragon/www/school-app/app/Http/Middleware/CheckRoleScope.php) | Memeriksa apakah pengguna memiliki salah satu peran yang berafiliasi dengan cakupan (*scope*) tertentu. |
| `applicant` | [EnsureIsApplicant.php](file:///d:/laragon/www/school-app/app/Http/Middleware/EnsureIsApplicant.php) | Memastikan akses portal pendaftaran hanya untuk akun pelamar/calon siswa baru. |

---

### 4. TEMUAN AUDIT KEAMANAN (SECURITY VULNERABILITIES)

Berdasarkan tinjauan arsitektur kode (*source code review*), berikut adalah daftar celah keamanan dan kelemahan implementasi yang ditemukan:

#### Temuan 1: Bypass Otorisasi pada Admin Dashboard (Kritis)
* **Lokasi Berkas:** [routes/web.php](file:///d:/laragon/www/school-app/routes/web.php#L82-L86) dan [DashboardController.php](file:///d:/laragon/www/school-app/app/Http/Controllers/Admin/DashboardController.php)
* **Kategori Dampak:** *Broken Object Level Authorization / Privilege Escalation / Data Leakage*
* **Deskripsi:**
  Rute untuk mengakses dashboard administrator utama didefinisikan sebagai berikut:
  ```php
  Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
      Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
  ```
  Rute ini hanya dilindungi oleh middleware `'auth'`, yang berarti rute tersebut terbuka untuk **semua pengguna terautentikasi**. 
  
  Pengguna dengan peran `Applicant` (pelamar sekolah) yang masuk melalui portal pendaftaran dapat dengan sengaja mengetikkan URL `/admin/dashboard` pada peramban mereka. Karena middleware `'auth'` terpenuhi dan tidak ada pemeriksaan hak akses tambahan pada `DashboardController`, mereka akan disajikan tampilan Dashboard Utama Administrator.
  
  Hal ini mengakibatkan kebocoran informasi krusial seperti statistik keuangan penerimaan siswa baru, jumlah pendaftar, dan data pribadi 10 pendaftar terbaru (nama, status pendaftaran, dll.), yang melanggar prinsip kerahasiaan data pribadi (UU PDP).

---

#### Temuan 2: Hardcoded Role Names pada Middleware EnsureIsApplicant (Sedang)
* **Lokasi Berkas:** [EnsureIsApplicant.php](file:///d:/laragon/www/school-app/app/Http/Middleware/EnsureIsApplicant.php#L24-L27)
* **Kategori Dampak:** *Tight Coupling / Vulnerability to Misconfiguration*
* **Deskripsi:**
  Dalam mengalihkan pengguna administrator dari area portal pelamar ke dashboard admin, middleware menuliskan nama peran secara eksplisit (*hardcoded*):
  ```php
  if (Auth::user()->hasRole('Developer') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Kepala Tata Usaha') || Auth::user()->hasRole('Staf Administrasi TU')) {
      return redirect()->route('admin.dashboard')
          ->with('info', 'Anda sudah login sebagai administrator.');
  }
  ```
  Ini merupakan *anti-pattern* dalam RBAC. Jika sistem menambahkan peran manajerial baru (misal: "Kepala Sekolah", "Panitia SPMB", atau "Bendahara"), peran tersebut tidak akan terdeteksi oleh middleware ini dan dapat memicu kegagalan fungsional alur login atau bahkan pengusiran paksa (*forced logout*) karena tidak lolos pengecekan peran berikutnya.

---

#### Temuan 3: Kerentanan Reset Password dengan Nilai Statis (Sedang)
* **Lokasi Berkas:** [UserController.php](file:///d:/laragon/www/school-app/app/Http/Controllers/Admin/Settings/UserController.php#L164-L175)
* **Kategori Dampak:** *Weak Password Authentication & Account Takeover*
* **Deskripsi:**
  Fitur reset password pengguna pada panel admin mengubah password akun secara langsung menjadi kata sandi statis `'password'`:
  ```php
  public function resetPassword($id)
  {
      try {
          $user = $this->repository->find($id);
          $user->update([
              'password' => Hash::make('password')
          ]);
          return response()->json(['message' => 'Password berhasil di-reset ke: password']);
  ```
  Jika administrator melakukan reset password untuk akun staf penting, kata sandi tersebut langsung terekspos dengan nilai yang sangat lemah dan umum. Hal ini memudahkan penyerang untuk melakukan pembajakan akun (*account takeover*) menggunakan teknik *brute force* atau menebak kata sandi sesaat setelah proses reset terjadi.

---

#### Temuan 4: Risiko Degradasi Performa & Abuse Eksekusi Artisan via Web Request (Rendah)
* **Lokasi Berkas:** [RoleController.php](file:///d:/laragon/www/school-app/app/Http/Controllers/Admin/Settings/RoleController.php#L109-L117)
* **Kategori Dampak:** *Denial of Service (DoS) Vector / Resource Exhaustion*
* **Deskripsi:**
  Aplikasi menyediakan tombol "Sync Permission" pada antarmuka admin yang memicu eksekusi perintah Artisan secara langsung:
  ```php
  public function syncPermissions()
  {
      try {
          \Illuminate\Support\Facades\Artisan::call('app:sync-permissions --prune');
          return response()->json(['message' => 'Sinkronisasi permission berhasil']);
  ```
  Memanggil `Artisan::call` dalam siklus hidup HTTP request bersifat sinkron dan memakan sumber daya CPU/database yang cukup besar karena harus membaca semua rute Laravel. Jika rute aplikasi bertambah banyak, HTTP request ini berpotensi mengalami *timeout* atau kehabisan memori. Selain itu, jika terjadi serangan spamming request ke endpoint ini oleh admin yang nakal, database dapat mengalami *deadlock* atau penurunan performa secara masif.

---

#### Temuan 5: Ketiadaan Validasi Privilege Escalation pada Update User/Role (Sedang)
* **Lokasi Berkas:** [RoleController.php](file:///d:/laragon/www/school-app/app/Http/Controllers/Admin/Settings/RoleController.php) dan [UserController.php](file:///d:/laragon/www/school-app/app/Http/Controllers/Admin/Settings/UserController.php)
* **Kategori Dampak:** *Privilege Escalation*
* **Deskripsi:**
  Seorang pengguna dengan peran "Staf Administrasi TU" atau sejenisnya yang memiliki izin `settings.user.edit` dapat mengubah data diri mereka atau pengguna lain. Namun, sistem tidak membatasi peran apa saja yang bisa mereka ubah/berikan.
  
  Tidak ada validasi untuk mencegah pengguna non-Developer memberikan peran `Developer` kepada diri mereka sendiri atau meloloskan scope `system` di luar wewenang mereka. Pengguna dengan hak akses edit user/role dapat dengan mudah melakukan eksploitasi kenaikan hak akses secara horizontal maupun vertikal.

---

### 5. REKOMENDASI PERBAIKAN & PANDUAN REMEDIASI

Berikut adalah langkah perbaikan taktis untuk menutup celah keamanan di atas:

#### Solusi Temuan 1: Pengamanan Rute Dashboard Admin
Batasi rute `/admin/dashboard` agar hanya dapat diakses oleh peran yang memiliki scope administratif (bukan `siswa` atau `applicant`).

* **Penerapan Perbaikan:**
  Ubah pendaftaran rute pada [routes/web.php](file:///d:/laragon/www/school-app/routes/web.php#L82-L86):
  ```php
  // Sebelum
  Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
      Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
  
  // Sesudah
  Route::prefix('admin')->name('admin.')->middleware(['auth', 'scope:system,manajemen,pimpinan,guru,tendik'])->group(function () {
      Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
  ```
  *Catatan: Pastikan `'scope'` terdaftar sebagai middleware yang memanggil `CheckRoleScope::class`.*

---

#### Solusi Temuan 2: Dinamisasi Otorisasi dengan Scope pada Middleware
Alih-alih menuliskan daftar peran administrasi secara kaku, manfaatkan fungsi `hasRoleScope` yang sudah disediakan oleh sistem.

* **Penerapan Perbaikan:**
  Ubah kode pemeriksaan pada [EnsureIsApplicant.php](file:///d:/laragon/www/school-app/app/Http/Middleware/EnsureIsApplicant.php#L24-L27):
  ```php
  // Sebelum
  if (Auth::user()->hasRole('Developer') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Kepala Tata Usaha') || Auth::user()->hasRole('Staf Administrasi TU')) {
      ...
  }

  // Sesudah (Lebih Dinamis & Aman)
  if (Auth::user()->hasRoleScope(['system', 'manajemen', 'pimpinan', 'tendik'])) {
      return redirect()->route('admin.dashboard')
          ->with('info', 'Anda sudah login sebagai administrator.');
  }
  ```

---

#### Solusi Temuan 3: Peningkatan Keamanan Reset Password
Gunakan generator password acak sekali pakai yang aman dan tampilkan kepada administrator yang melakukan tindakan reset, atau paksa pengubahan password saat login pertama kali.

* **Penerapan Perbaikan:**
  Ganti logika reset pada [UserController.php](file:///d:/laragon/www/school-app/app/Http/Controllers/Admin/Settings/UserController.php#L164-L175):
  ```php
  public function resetPassword($id)
  {
      try {
          $user = $this->repository->find($id);
          $temporaryPassword = bin2hex(random_bytes(4)); // Menghasilkan password acak 8 karakter seperti "f3a2b5c8"
          
          $user->update([
              'password' => Hash::make($temporaryPassword)
          ]);
          
          return response()->json([
              'message' => 'Password berhasil di-reset.',
              'temporary_password' => $temporaryPassword // Kirim balik password baru untuk ditampilkan sekali di UI
          ]);
      } catch (\Exception $e) { ... }
  }
  ```

---

#### Solusi Temuan 4: Pembatasan Throttling & Penanganan Asinkron Perintah Sync
Untuk menghindari penyalahgunaan, terapkan middleware `throttle` pada rute sinkronisasi izin akses, atau jalankan melalui Laravel Queue. Namun jika masih ingin dipertahankan secara sinkron, tambahkan pembatasan rate limit HTTP yang ketat.

* **Penerapan Perbaikan:**
  Tambahkan rate-limiting di [routes/web.php](file:///d:/laragon/www/school-app/routes/web.php#L131):
  ```php
  Route::post('/sync-permissions', 'syncPermissions')
      ->name('sync')
      ->middleware(['permission:settings.rbac.create', 'throttle:1,5']); // Maksimal 1 kali eksekusi setiap 5 menit
  ```

---

#### Solusi Temuan 5: Proteksi Eskalasi Peran (*Privilege Escalation Protection*)
Batasi hak akses untuk mengubah peran (role) dan pengguna (user). Hanya admin berscope `system` (Developer) yang boleh memberikan peran berscope `system` kepada pengguna lain.

* **Penerapan Perbaikan:**
  Tambahkan validasi keamanan di [UserController.php](file:///d:/laragon/www/school-app/app/Http/Controllers/Admin/Settings/UserController.php#L74-L82):
  ```php
  // Pastikan request roles tidak mengandung peran 'Developer' jika pengubah bukanlah Developer
  if (in_array('Developer', $request->roles) && !auth()->user()->hasRole('Developer')) {
      return response()->json(['message' => 'Anda tidak memiliki hak untuk memberikan peran Developer!'], 403);
  }
  ```

---

### KESIMPULAN
Secara arsitektural, rancangan sistem RBAC pada aplikasi ini sudah cukup matang dan terstruktur berkat pemisahan logika repository serta integrasi Spatie Permission. Namun, **celah kritis pada rute `/admin/dashboard` wajib segera ditambal** sebelum aplikasi dideploy ke lingkungan produksi guna menghindari kebocoran data pelamar sekolah yang berisiko menyeret implikasi hukum.
