<?php

namespace App\Services;

use App\Models\ApplicantEnrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PdfGeneratorService
{
    /**
     * Generate Surat Pernyataan Siswa Tetap PDF.
     *
     * @param ApplicantEnrollment $enrollment
     * @return string Absolute path ke file PDF yang telah disimpan
     * @throws \RuntimeException jika PDF gagal dibuat atau disimpan
     */
    public function generateEnrollmentLetter(ApplicantEnrollment $enrollment): string
    {
        // Ambil identitas sekolah dari tabel school_identities
        // Kolom utama: school_name, address, phone, email, headmaster_name, headmaster_nip
        $schoolIdentity = DB::table('school_identities')->first();

        // Pastikan relasi yang dibutuhkan template sudah ter-load
        $enrollment->loadMissing(['applicant', 'spmbTrack.trackType']);

        $applicantName = $enrollment->applicant->full_name;
        $year = date('Y');

        // Format nama file: SuratPernyataan_{NamaSiswa}_{TahunAjaran}.pdf
        $fileName  = 'SuratPernyataan_' . Str::slug($applicantName) . '_' . $year . '-' . ($year + 1) . '.pdf';
        $directory = 'spmb/surat_pernyataan';

        $data = [
            'enrollment'     => $enrollment,
            'schoolIdentity' => $schoolIdentity,
            'date'           => now()->translatedFormat('d F Y'),
        ];

        // Generate PDF menggunakan dompdf
        $pdf = Pdf::loadView('pdf.surat_pernyataan', $data);
        $pdfContent = $pdf->output();

        if (empty($pdfContent)) {
            throw new \RuntimeException('PDF generation menghasilkan konten kosong. Periksa template surat_pernyataan.blade.php');
        }

        // Simpan ke disk 'public' (storage/app/public/) agar path konsisten
        // Path relatif terhadap root disk 'public': spmb/surat_pernyataan/{filename}
        $disk        = Storage::disk('public');
        $storagePath = $directory . '/' . $fileName;

        // Pastikan direktori ada (Storage driver local otomatis buat subdirektori)
        if (!$disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $saved = $disk->put($storagePath, $pdfContent);

        if (!$saved) {
            throw new \RuntimeException('Gagal menyimpan file PDF ke storage. Periksa permission direktori storage/app/public/');
        }

        // Kembalikan path absolut untuk dipakai response()->download()
        return storage_path('app/public/' . $storagePath);
    }

    /**
     * Generate Kartu Ujian Peserta PDF.
     *
     * @param ApplicantEnrollment $enrollment
     * @return string Absolute path ke file PDF yang telah disimpan
     * @throws \RuntimeException jika PDF gagal dibuat atau disimpan
     */
    public function generateTestCard(ApplicantEnrollment $enrollment): string
    {
        $schoolIdentity = DB::table('school_identities')->first();

        // Pastikan relasi yang dibutuhkan template sudah ter-load
        $enrollment->loadMissing(['applicant', 'spmbTrack.trackType']);

        $applicantName = $enrollment->applicant->full_name;
        $year = date('Y');

        // Format nama file: KartuUjian_{NamaSiswa}_{TahunAjaran}.pdf
        $fileName  = 'KartuUjian_' . Str::slug($applicantName) . '_' . $year . '-' . ($year + 1) . '.pdf';
        $directory = 'spmb/kartu_ujian';

        $data = [
            'enrollment'     => $enrollment,
            'schoolIdentity' => $schoolIdentity,
            'date'           => now()->translatedFormat('d F Y'),
        ];

        // Generate PDF menggunakan dompdf
        $pdf = Pdf::loadView('pdf.kartu_ujian', $data);
        $pdfContent = $pdf->output();

        if (empty($pdfContent)) {
            throw new \RuntimeException('PDF generation menghasilkan konten kosong. Periksa template kartu_ujian.blade.php');
        }

        $disk        = Storage::disk('public');
        $storagePath = $directory . '/' . $fileName;

        if (!$disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $saved = $disk->put($storagePath, $pdfContent);

        if (!$saved) {
            throw new \RuntimeException('Gagal menyimpan file PDF ke storage. Periksa permission direktori storage/app/public/');
        }

        return storage_path('app/public/' . $storagePath);
    }
}
