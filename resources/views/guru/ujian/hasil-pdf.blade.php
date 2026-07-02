<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai - {{ $ujian->judul }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            background-color: #fff;
            margin: 20px;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 5px 0 0;
            font-size: 16px;
            font-weight: normal;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #555;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .info-table td {
            padding: 4px 0;
        }
        .info-table td.label {
            width: 150px;
            font-weight: bold;
        }
        .info-table td.colon {
            width: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 13px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px 10px;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
            font-size: 13px;
        }
        .signature-section td {
            width: 50%;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- Header / Kop Surat -->
    <div class="header">
        <h2>MADRASAH TSANAWIYAH (MTs) SYAFIIYAH</h2>
        <h3>REKAPITULASI NILAI UJIAN ONLINE</h3>
        <p>Alamat: Jl. Raya Pendidikan No. 45, Kecamatan Syafiiyah, Jawa Timur</p>
    </div>

    <!-- Informasi Ujian -->
    <table class="info-table">
        <tr>
            <td class="label">Judul Ujian</td>
            <td class="colon">:</td>
            <td>{{ $ujian->judul }}</td>
            <td class="label">Mata Pelajaran</td>
            <td class="colon">:</td>
            <td>{{ $ujian->mapel->nama_mapel }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td>{{ $ujian->kelas->nama_kelas }}</td>
            <td class="label">Guru Pengampu</td>
            <td class="colon">:</td>
            <td>{{ $ujian->guru->name }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Ujian</td>
            <td class="colon">:</td>
            <td>{{ $ujian->waktu_mulai->format('d M Y') }}</td>
            <td class="label">Durasi</td>
            <td class="colon">:</td>
            <td>{{ $ujian->durasi }} Menit</td>
        </tr>
    </table>

    <!-- Tabel Nilai -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">No</th>
                <th style="width: 150px;">NIS</th>
                <th>Nama Siswa</th>
                <th class="text-center" style="width: 150px;">Status</th>
                <th class="text-right" style="width: 120px;">Nilai Ujian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                @php
                    $sesi = $ujianSiswas->get($siswa->id);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td class="font-bold">{{ $siswa->nama }}</td>
                    <td class="text-center">
                        @if(!$sesi)
                            Belum Ujian
                        @elseif($sesi->status == 'mengerjakan')
                            Sedang Mengerjakan
                        @else
                            Selesai
                        @endif
                    </td>
                    <td class="text-right font-bold">
                        @if($sesi && $sesi->status == 'selesai')
                            {{ number_format($sesi->nilai, 1) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="signature-section">
        <tr>
            <td></td>
            <td style="text-align: right; padding-right: 50px;">
                <p>Syafiiyah, {{ date('d M Y') }}</p>
                <p style="margin-bottom: 60px;">Guru Pengampu,</p>
                <p class="font-bold" style="text-decoration: underline;">{{ $ujian->guru->name }}</p>
                <p>NIP. ..................................</p>
            </td>
        </tr>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
