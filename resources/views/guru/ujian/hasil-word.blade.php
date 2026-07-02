<html xmlns:o='urn:schemas-microsoft-com:office:office' 
      xmlns:w='urn:schemas-microsoft-com:office:word' 
      xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai - {{ $ujian->judul }}</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #000000;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 4px double #000000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .kop-title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .kop-subtitle {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0 0 0;
        }
        .kop-address {
            font-size: 9pt;
            margin: 5px 0 0 0;
            font-style: italic;
        }
        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }
        .info-table td {
            padding: 3px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 130px;
        }
        .info-colon {
            width: 10px;
        }
        .table-title {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
            text-align: center;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5pt;
        }
        .data-table th, .data-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
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
        .signature-table {
            width: 100%;
            margin-top: 40px;
            font-size: 11pt;
        }
        .signature-td {
            width: 50%;
            text-align: left;
        }
        .signature-right {
            text-align: right;
            padding-right: 20px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <div class="kop-title">MADRASAH TSANAWIYAH (MTs) SYAFIIYAH</div>
        <div class="kop-subtitle">REKAPITULASI NILAI UJIAN ONLINE</div>
        <div class="kop-address">Alamat: Jl. Raya Pendidikan No. 45, Kecamatan Syafiiyah, Jawa Timur</div>
    </div>

    <!-- Info Detail Ujian -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Judul Ujian</td>
                <td class="info-colon">:</td>
                <td>{{ $ujian->judul }}</td>
                <td class="info-label">Mata Pelajaran</td>
                <td class="info-colon">:</td>
                <td>{{ $ujian->mapel->nama_mapel }}</td>
            </tr>
            <tr>
                <td class="info-label">Kelas</td>
                <td class="info-colon">:</td>
                <td>{{ $ujian->kelas->nama_kelas }}</td>
                <td class="info-label">Guru Pengampu</td>
                <td class="info-colon">:</td>
                <td>{{ $ujian->guru->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Ujian</td>
                <td class="info-colon">:</td>
                <td>{{ $ujian->waktu_mulai->format('d M Y') }}</td>
                <td class="info-label">Durasi Ujian</td>
                <td class="info-colon">:</td>
                <td>{{ $ujian->durasi }} Menit</td>
            </tr>
        </table>
    </div>

    <div class="table-title">DAFTAR NILAI SISWA</div>

    <!-- Data Nilai -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th style="width: 150px;">NIS</th>
                <th>Nama Siswa</th>
                <th class="text-center" style="width: 150px;">Status</th>
                <th class="text-right" style="width: 120px;">Nilai Akhir</th>
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
    <table class="signature-table">
        <tr>
            <td class="signature-td"></td>
            <td class="signature-td signature-right">
                <p>Syafiiyah, {{ date('d M Y') }}</p>
                <p style="margin-bottom: 60px;">Guru Pengampu,</p>
                <p class="font-bold" style="text-decoration: underline;">{{ $ujian->guru->name }}</p>
                <p>NIP. ........................................</p>
            </td>
        </tr>
    </table>

</body>
</html>
