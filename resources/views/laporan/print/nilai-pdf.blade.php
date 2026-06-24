<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Kelas {{ $selectedKelas->kode_kelas }} - {{ $selectedMapel->nama_mapel }}</title>
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

    <div class="title">Rekapitulasi Nilai Mata Pelajaran</div>

    <table class="info-table">
        <tr>
            <td style="width: 18%;"><strong>Kelas</strong></td>
            <td style="width: 32%;">: {{ $selectedKelas->kode_kelas }}</td>
            <td style="width: 18%;"><strong>Mata Pelajaran</strong></td>
            <td style="width: 32%;">: {{ $selectedMapel->nama_mapel }}</td>
        </tr>
        <tr>
            <td><strong>Tingkat / Jurusan</strong></td>
            <td>: {{ $selectedKelas->tingkat }} / {{ $selectedKelas->jurusan }}</td>
            <td><strong>Guru Pengampu</strong></td>
            <td>: {{ $selectedMapel->guru ? $selectedMapel->guru->name : 'Belum ditentukan' }}</td>
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
                <th style="width: 11%;">NIS</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 10%;" class="text-center">Rata Harian (40%)</th>
                <th style="width: 10%;" class="text-center">UTS (30%)</th>
                <th style="width: 8%;" class="text-center">Rank UTS</th>
                <th style="width: 10%;" class="text-center">UAS (30%)</th>
                <th style="width: 8%;" class="text-center">Rank UAS</th>
                <th style="width: 8%;" class="text-center">Akhir</th>
                <th style="width: 8%;" class="text-center">Rank Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswa as $index => $item)
                @php
                    $grade = $grades->get($item->id);
                    $harian = $grade ? $grade->nilai_harian : null;
                    $uts = $grade ? $grade->nilai_uts : null;
                    $uas = $grade ? $grade->nilai_uas : null;
                    
                    $nilaiAkhir = '-';
                    if ($harian !== null || $uts !== null || $uas !== null) {
                        $nilaiAkhir = round(($harian ?? 0) * 0.4 + ($uts ?? 0) * 0.3 + ($uas ?? 0) * 0.3);
                    }

                    $rankUts = $utsRanks[$item->id] ?? '-';
                    $rankUas = $uasRanks[$item->id] ?? '-';
                    $rankFinal = $finalRanks[$item->id] ?? '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nis }}</td>
                    <td><strong>{{ $item->nama }}</strong></td>
                    <td class="text-center">{{ $harian !== null ? number_format($harian, 1) : '-' }}</td>
                    <td class="text-center">{{ $uts ?? '-' }}</td>
                    <td class="text-center">#{{ $rankUts }}</td>
                    <td class="text-center">{{ $uas ?? '-' }}</td>
                    <td class="text-center">#{{ $rankUas }}</td>
                    <td class="text-center"><strong>{{ $nilaiAkhir }}</strong></td>
                    <td class="text-center"><strong>#{{ $rankFinal }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

