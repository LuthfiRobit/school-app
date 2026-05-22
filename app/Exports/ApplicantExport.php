<?php

namespace App\Exports;

use App\Models\ApplicantEnrollment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicantExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        $query = ApplicantEnrollment::query()
            ->with(['applicant', 'spmbTrack.trackType', 'spmbTrack.spmbConfiguration', 'invoices', 'statusLogs'])
            ->whereNotIn('status', ['draft']); // Jangan export data yang masih draft

        // Filter by Academic Year
        if (!empty($this->filters['academic_year_id'])) {
            $query->whereHas('spmbTrack.spmbConfiguration', function ($q) {
                $q->where('academic_year_id', $this->filters['academic_year_id']);
            });
        }

        // Filter by Track
        if (!empty($this->filters['spmb_track_id'])) {
            $query->where('spmb_track_id', $this->filters['spmb_track_id']);
        }

        // Filter by Status
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No. Pendaftaran',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Nama Orang Tua',
            'No. Telepon',
            'Jalur Pendaftaran',
            'Status Pendaftaran',
            'Tanggal Daftar',
            'Tanggal Diterima',
            'Total Tagihan (Rp)',
            'Total Dibayar (Rp)',
            'Status Pembayaran',
        ];
    }

    /**
     * @param mixed $enrollment
     * @return array
     */
    public function map($enrollment): array
    {
        $totalTagihan = $enrollment->invoices->sum('total_amount');
        $totalDibayar = $enrollment->invoices->sum('paid_amount');
        
        $statusPembayaran = 'Belum Ada Tagihan';
        if ($totalTagihan > 0) {
            if ($totalDibayar >= $totalTagihan) {
                $statusPembayaran = 'Lunas';
            } elseif ($totalDibayar > 0) {
                $statusPembayaran = 'Sebagian (' . round(($totalDibayar / $totalTagihan) * 100) . '%)';
            } else {
                $statusPembayaran = 'Belum Dibayar';
            }
        }

        return [
            $enrollment->enrollment_number,
            $enrollment->applicant->full_name ?? '-',
            $enrollment->applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $enrollment->applicant->date_of_birth ? \Carbon\Carbon::parse($enrollment->applicant->date_of_birth)->format('d M Y') : '-',
            $enrollment->applicant->parent_name ?? '-',
            $enrollment->applicant->phone ?? '-',
            $enrollment->spmbTrack->trackType->name ?? '-',
            $enrollment->status->label(),
            $enrollment->created_at->format('d M Y H:i'),
            $enrollment->enrolled_at ? \Carbon\Carbon::parse($enrollment->enrolled_at)->format('d M Y') : '-',
            $totalTagihan,
            $totalDibayar,
            $statusPembayaran,
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Data Pendaftar';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
