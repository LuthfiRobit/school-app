<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penerimaan Siswa Baru</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .school-address {
            font-size: 11px;
            margin: 5px 0 0 0;
        }
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-decoration: underline;
        }
        .report-subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            background-color: #f0f0f0;
            padding: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .kpi-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .kpi-box {
            display: inline-block;
            width: 23%;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
        }
        .kpi-label {
            font-size: 10px;
            color: #666;
        }

        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-date {
            margin-bottom: 50px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="school-name">{{ $schoolIdentity->school_name ?? 'NAMA SEKOLAH' }}</div>
        <div class="school-address">
            {{ $schoolIdentity->address ?? 'Alamat Sekolah' }}<br>
            Telp: {{ $schoolIdentity->phone ?? '-' }} | Email: {{ $schoolIdentity->email ?? '-' }}
        </div>
    </div>

    <div class="report-title">LAPORAN PENERIMAAN SISWA BARU</div>
    <div class="report-subtitle">
        Tahun Ajaran: {{ $academicYear ? $academicYear->name : 'Semua Tahun Ajaran' }}
    </div>

    <!-- KPI Section -->
    <div class="section-title">1. Ringkasan Eksekutif</div>
    <div class="kpi-container">
        <div class="kpi-box">
            <div class="kpi-label">Pendaftar Aktif</div>
            <div class="kpi-value">{{ number_format($kpi['total_pendaftar'] ?? 0) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Lulus Seleksi</div>
            <div class="kpi-value">{{ number_format($kpi['total_lulus'] ?? 0) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Siswa Tetap</div>
            <div class="kpi-value">{{ number_format($kpi['total_siswa_tetap'] ?? 0) }}</div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Total Penerimaan</div>
            <div class="kpi-value">Rp {{ number_format($kpi['total_penerimaan'] ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tracks Table -->
    <div class="section-title">2. Detail Berdasarkan Jalur Pendaftaran</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Jalur Pendaftaran</th>
                <th class="text-right" width="15%">Kuota</th>
                <th class="text-right" width="15%">Pendaftar</th>
                <th class="text-right" width="15%">Lulus (Siswa Tetap)</th>
                <th class="text-center" width="15%">% Terisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tracks as $index => $track)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $track['name'] }}</td>
                <td class="text-right">{{ number_format($track['quota']) }}</td>
                <td class="text-right">{{ number_format($track['total_active']) }}</td>
                <td class="text-right">{{ number_format($track['total_passed']) }}</td>
                <td class="text-center">{{ $track['fill_percentage'] }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada data pendaftar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Status Distribution -->
    <div class="section-title">3. Distribusi Status Pendaftar</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="75%">Status</th>
                <th class="text-right" width="20%">Jumlah Pendaftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($status_distribution as $index => $status)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $status['label'] }}</td>
                <td class="text-right">{{ number_format($status['total']) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer / Signature -->
    <div class="footer">
        <div class="signature-box">
            <div class="signature-date">Probolinggo, {{ $date }}</div>
            <div>Kepala Sekolah,</div>
            <div style="height: 60px;"></div>
            <div class="signature-name">{{ $schoolIdentity->headmaster_name ?? '____________________' }}</div>
            <div>NIP. {{ $schoolIdentity->headmaster_nip ?? '-' }}</div>
        </div>
    </div>

</body>
</html>
