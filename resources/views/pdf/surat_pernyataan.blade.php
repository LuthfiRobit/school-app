<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Siswa Tetap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .content {
            margin-bottom: 30px;
        }
        .table-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .table-info td {
            padding: 5px;
            vertical-align: top;
        }
        .table-info td:first-child {
            width: 30%;
        }
        .table-info td:nth-child(2) {
            width: 5%;
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 10px;
            height: 100px;
        }
    </style>
</head>
<body>
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
        SURAT PERNYATAAN SISWA TETAP
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, menerangkan bahwa siswa dengan data sebagai berikut:</p>

        <table class="table-info">
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td><strong>{{ $enrollment->applicant->full_name }}</strong></td>
            </tr>
            <tr>
                <td>Nomor Pendaftaran</td>
                <td>:</td>
                <td>{{ $enrollment->enrollment_number }}</td>
            </tr>
            <tr>
                <td>Jalur Pendaftaran</td>
                <td>:</td>
                <td>{{ $enrollment->spmbTrack->trackType->name }}</td>
            </tr>
            <tr>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td>{{ date('Y') }}/{{ date('Y') + 1 }}</td>
            </tr>
        </table>

        <p>Telah melengkapi seluruh persyaratan administrasi dan pembayaran Daftar Ulang. Dengan demikian, siswa tersebut dinyatakan secara resmi diterima sebagai <strong>Siswa Tetap</strong> di sekolah kami untuk Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}.</p>
        <p>Demikian surat pernyataan ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <div class="footer">
        @php
            // Ambil kota dari address jika tersedia, fallback ke default
            $city = isset($schoolIdentity) && $schoolIdentity->address
                ? explode(',', $schoolIdentity->address)[1] ?? 'Kota Pelajar'
                : 'Kota Pelajar';
            $city = trim($city);
            $signerName = isset($schoolIdentity) ? ($schoolIdentity->headmaster_name ?? '( _________________________ )') : '( _________________________ )';
        @endphp
        <p>Ditetapkan di: {{ $city }}</p>
        <p>Pada tanggal: {{ $date }}</p>
        <br>
        <p>Kepala Sekolah,</p>
        <div class="signature">
            <!-- Tanda Tangan / Stempel bisa disisipkan di sini jika ada -->
            <br><br><br><br>
        </div>
        <p><strong>{{ $signerName }}</strong></p>
        @if(isset($schoolIdentity) && $schoolIdentity->headmaster_nip)
            <p>NIP. {{ $schoolIdentity->headmaster_nip }}</p>
        @endif
    </div>
</body>
</html>
