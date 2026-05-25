<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Ujian Peserta SPMB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .card-container {
            border: 2px solid #000;
            padding: 20px;
            width: 90%;
            margin: 0 auto;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header h1, .header h2, .header p {
            margin: 0;
            padding: 0;
        }
        .header h1 {
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14pt;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 20px;
            background-color: #f0f0f0;
            padding: 8px;
            border: 1px solid #000;
        }
        .content-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .content-table td {
            padding: 8px;
            vertical-align: top;
        }
        .content-table .label {
            width: 35%;
            font-weight: bold;
        }
        .content-table .separator {
            width: 5%;
            text-align: center;
        }
        .content-table .value {
            width: 60%;
        }
        .photo-box {
            width: 100px;
            height: 130px;
            border: 1px solid #000;
            text-align: center;
            line-height: 130px;
            float: right;
            margin-left: 20px;
            color: #666;
            font-size: 10pt;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .signature {
            margin-top: 10px;
            height: 80px;
        }
        .instructions {
            margin-top: 30px;
            font-size: 9pt;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        .instructions h4 {
            margin: 0 0 5px 0;
        }
        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="card-container">
        <div class="header">
            @if(isset($schoolIdentity))
                <h1>{{ $schoolIdentity->school_name ?? 'NAMA SEKOLAH' }}</h1>
                <p>{{ $schoolIdentity->address ?? 'Alamat Sekolah' }}</p>
                <p>Telp: {{ $schoolIdentity->phone ?? '-' }} | Email: {{ $schoolIdentity->email ?? '-' }}</p>
            @else
                <h1>SEKOLAH TUNAS LUHUR</h1>
                <p>Jalan Pendidikan No. 1, Kota Pelajar</p>
                <p>Telp: (021) 1234567 | Email: info@tunasluhur.sch.id</p>
            @endif
        </div>

        <div class="title">
            KARTU TANDA PESERTA UJIAN SELEKSI
        </div>

        <div class="photo-box">
            Pas Foto 3x4
        </div>

        <table class="content-table">
            <tr>
                <td class="label">Nomor Pendaftaran</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ $enrollment->enrollment_number }}</strong></td>
            </tr>
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="separator">:</td>
                <td class="value"><strong>{{ strtoupper($enrollment->applicant->full_name) }}</strong></td>
            </tr>
            <tr>
                <td class="label">Jalur Pendaftaran</td>
                <td class="separator">:</td>
                <td class="value">{{ $enrollment->spmbTrack->trackType->name }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Ajaran</td>
                <td class="separator">:</td>
                <td class="value">{{ date('Y') }}/{{ date('Y') + 1 }}</td>
            </tr>
        </table>

        <div style="clear: both;"></div>

        <div class="footer">
            @php
                $city = isset($schoolIdentity) && $schoolIdentity->address
                    ? explode(',', $schoolIdentity->address)[1] ?? 'Kota Pelajar'
                    : 'Kota Pelajar';
                $city = trim($city);
            @endphp
            <p>Dikeluarkan di: {{ $city }}</p>
            <p>Pada tanggal: {{ $date }}</p>
            <br>
            <p>Panitia SPMB,</p>
            <div class="signature">
                <br><br><br>
            </div>
            <p><strong>( _________________________ )</strong></p>
        </div>

        <div class="instructions">
            <h4>Tata Tertib & Perhatian:</h4>
            <ul>
                <li>Kartu ini wajib dibawa saat pelaksanaan ujian/seleksi.</li>
                <li>Peserta wajib menempelkan pas foto berwarna ukuran 3x4 pada kolom yang disediakan (jika belum tercetak).</li>
                <li>Peserta hadir selambat-lambatnya 30 menit sebelum ujian dimulai.</li>
                <li>Membawa alat tulis dan perlengkapan ujian yang diperlukan.</li>
            </ul>
        </div>
    </div>
</body>
</html>
