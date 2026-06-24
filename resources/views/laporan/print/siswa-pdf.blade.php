<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Siswa - {{ $selectedKelas->kode_kelas }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 20px; }
        .kop-surat h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .kop-surat h3 { margin: 4px 0 0 0; font-size: 12px; }
        .kop-surat p { margin: 4px 0 0 0; font-size: 10px; font-style: italic; }
        .title { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; }
        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 3px 0; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 5px 6px; text-align: left; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        @media print {
            body { margin: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="kop-surat">
        <h2>Madrasah Tsanawiyah Syafiiyah</h2>
        <h3>Status: TERAKREDITASI A</h3>
        <p>Jl. Raya Besuk No. 247 Besukkidul, Besuk Probolinggo 67283 Jawa Timur</p>
    </div>

    <div class="title">Laporan Data Siswa Kelas</div>

    <table class="info-table">
        <tr>
            <td style="width: 15%;"><strong>Kelas</strong></td>
            <td style="width: 35%;">: {{ $selectedKelas->kode_kelas }}</td>
            <td style="width: 15%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 35%;">: {{ date('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Tingkat / Jurusan</strong></td>
            <td>: {{ $selectedKelas->tingkat }} / {{ $selectedKelas->jurusan }}</td>
            <td><strong>Total Siswa</strong></td>
            <td>: {{ $siswa->count() }} Orang</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 15%;">NISN</th>
                <th style="width: 35%;">Nama Siswa</th>
                <th style="width: 10%;" class="text-center">L/P</th>
                <th style="width: 20%;">No. Telp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswa as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nis }}</td>
                <td>{{ $item->nisn ?? '-' }}</td>
                <td><strong>{{ $item->nama }}</strong></td>
                <td class="text-center">{{ $item->jenis_kelamin }}</td>
                <td>{{ $item->telp ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

