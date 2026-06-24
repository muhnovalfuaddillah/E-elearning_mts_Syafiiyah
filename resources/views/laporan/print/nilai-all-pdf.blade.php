<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Kelas {{ $selectedKelas->kode_kelas }} - Semua Mapel</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; margin: 15px; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .kop-surat h2 { margin: 0; font-size: 15px; text-transform: uppercase; }
        .kop-surat h3 { margin: 4px 0 0 0; font-size: 11px; }
        .kop-surat p { margin: 4px 0 0 0; font-size: 9px; font-style: italic; }
        .title { text-align: center; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 12px; }
        .info-table { width: 100%; margin-bottom: 12px; }
        .info-table td { padding: 2px 0; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px 5px; text-align: left; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
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

    <div class="title">Rekapitulasi Nilai Akhir Seluruh Mata Pelajaran</div>

    <table class="info-table">
        <tr>
            <td style="width: 18%;"><strong>Kelas</strong></td>
            <td style="width: 32%;">: {{ $selectedKelas->kode_kelas }}</td>
            <td style="width: 18%;"><strong>Tingkat / Jurusan</strong></td>
            <td style="width: 32%;">: {{ $selectedKelas->tingkat }} / {{ $selectedKelas->jurusan }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ date('d F Y') }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 12%;">NIS</th>
                <th>Nama Siswa</th>
                @foreach($allMapels as $m)
                    <th class="text-center">{{ $m->nama_mapel }}</th>
                @endforeach
                <th style="width: 10%;" class="text-center">Rata-rata</th>
                <th style="width: 10%;" class="text-center">Peringkat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswa as $index => $item)
                @php
                    $studentGrades = $grades->get($item->id, collect());
                    $avg = $averages[$item->id] ?? 0;
                    $rank = $classRanks[$item->id] ?? '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nis }}</td>
                    <td><strong>{{ $item->nama }}</strong></td>
                    @foreach($allMapels as $m)
                        @php
                            $g = $studentGrades->firstWhere('mapel_id', $m->id);
                        @endphp
                        <td class="text-center">{{ $g ? ($g->nilai_akhir ?? '-') : '-' }}</td>
                    @endforeach
                    <td class="text-center" style="font-weight: bold;">{{ round($avg, 1) }}</td>
                    <td class="text-center" style="font-weight: bold;">#{{ $rank }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

