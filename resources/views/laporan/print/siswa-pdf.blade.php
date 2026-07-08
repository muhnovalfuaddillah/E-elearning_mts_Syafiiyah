<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Siswa - {{ $selectedKelas->kode_kelas }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ================= PRINT RULES ================= */
        @media print {
            body {
                background-color: #ffffff;
                color: #0f172a;
            }
            .no-print {
                display: none !important;
            }
            .print-padding {
                padding: 0 !important;
            }
            .document-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
            /* Ulangi header tabel di setiap halaman */
            table thead {
                display: table-header-group;
            }
            table tfoot {
                display: table-footer-group;
            }
            /* Cegah baris terpotong di tengah antar halaman */
            tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            /* Cegah blok tanda tangan terpotong */
            .signature-block {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            /* Nomor surat & footer tetap rapi */
            .doc-footer {
                page-break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body class="p-4 md:p-8 print-padding">

    <!-- Floating Action Buttons (No Print) -->
    <div class="max-w-4xl mx-auto mb-6 no-print flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <button onclick="window.history.back()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-lg shadow-blue-500/20 transition-all hover:scale-[1.02]">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Main Document Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-3xl shadow-xl shadow-slate-100 border border-slate-100 document-card">

        <!-- ==================== KOP SURAT RESMI (LETTERHEAD) ==================== -->
        <div class="flex items-center justify-between pb-4 border-b-4 border-double border-slate-900 gap-4 mb-2">
            <!-- Logo Madrasah -->
            <div class="w-20 h-20 bg-slate-100 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 text-3xl font-bold shrink-0">
                <img src="https://mts-syafiiyah.yasbahu.sch.id/logo.jpeg" alt="Logo MTs Syafiiyah" class="w-16 h-16 object-contain">
            </div>
            <!-- Letterhead Text -->
            <div class="text-center flex-1">
                <h2 class="font-extrabold text-lg md:text-xl uppercase tracking-wider text-slate-800">Yayasan Pendidikan Islam Syafiiyah</h2>
                <h1 class="font-black text-xl md:text-2xl uppercase tracking-widest text-blue-800 leading-tight">MTs Syafiiyah</h1>
                <p class="text-xs text-slate-500 font-medium">Jl. KH. Syafii No. 45, Besuk, Probolinggo, Jawa Timur | Telp: (0335) 123456</p>
                <p class="text-[10px] text-slate-400">Email: info@mtssyafiiyah.sch.id | Website: portal.mtssyafiiyah.sch.id</p>
            </div>
            <!-- Balancing empty space -->
            <div class="w-20 h-20 opacity-0 shrink-0 hidden md:block"></div>
        </div>

        <!-- Nomor Surat -->
        <div class="flex justify-between items-center mb-6 pb-3 border-b border-slate-200 text-[11px] text-slate-500 font-semibold">
            <p>Nomor: {{ $nomorSurat ?? str_pad(rand(1,999), 3, '0', STR_PAD_LEFT) . '/MTsS/SIS/' . \Carbon\Carbon::now()->format('m/Y') }}</p>
            <p>Sifat: Laporan Rutin</p>
        </div>

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h3 class="font-extrabold text-base md:text-lg uppercase tracking-wider text-slate-800 underline">Laporan Rekapitulasi Data Siswa</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Tahun Ajaran {{ $activeTahun ? $activeTahun->nama_tahun : '2025/2026' }}</p>
        </div>

        <!-- Metadata Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 bg-slate-50 p-5 rounded-2xl border border-slate-100">
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Kelas</span>
                <p class="text-blue-600 font-bold text-sm">{{ $selectedKelas->kode_kelas }}</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Tingkat / Jurusan</span>
                <p class="text-slate-800 font-bold text-sm">{{ $selectedKelas->tingkat }} / {{ $selectedKelas->jurusan }}</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Total Siswa</span>
                <p class="text-slate-800 font-bold text-sm">{{ $siswa->count() }} Orang</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Tanggal Cetak</span>
                <p class="text-slate-700 font-semibold text-xs leading-tight">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- Siswa Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-100 mb-4">
            <table class="w-full text-xs md:text-sm">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="p-3 text-center font-bold w-12">No</th>
                        <th class="p-3 text-center font-bold w-28">NIS</th>
                        <th class="p-3 text-center font-bold w-28">NISN</th>
                        <th class="p-3 text-left font-bold">Nama Siswa</th>
                        <th class="p-3 text-center font-bold w-20">L/P</th>
                        <th class="p-3 text-left font-bold w-36">No. Telp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($siswa as $index => $item)
                        <tr class="hover:bg-slate-50 transition-colors {{ $index % 2 === 1 ? 'bg-slate-50/50' : '' }}">
                            <td class="p-3 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="p-3 text-center font-semibold text-slate-700">{{ $item->nis }}</td>
                            <td class="p-3 text-center text-slate-600">{{ $item->nisn ?? '-' }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ $item->nama }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] {{ $item->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                    {{ $item->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600">{{ $item->telp ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400 italic">
                                <i class="fas fa-folder-open text-4xl mb-2 text-slate-200 block"></i>
                                Tidak ada data siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Ringkasan Total -->
        <div class="flex justify-end mb-10">
            <div class="bg-slate-50 border border-slate-100 rounded-xl px-5 py-2.5 text-xs font-bold text-slate-600">
                Total Siswa Terdaftar: <span class="text-blue-600">{{ $siswa->count() }}</span> Orang
            </div>
        </div>

        <!-- ==================== BLOK TANDA TANGAN ==================== -->
        <div class="signature-block mt-6">
            <div class="flex justify-end mb-6">
                <p class="text-xs text-slate-600 font-medium text-center w-64">
                    Probolinggo, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <!-- Kepala Madrasah -->
                <div class="space-y-1">
                    <p class="font-semibold text-slate-600">Mengetahui,</p>
                    <p class="font-bold text-slate-800 uppercase tracking-wide">Kepala MTs Syafiiyah</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-slate-800 underline">{{ $kepalaMadrasah ?? 'H. Sholehuddin, S. Ag' }}</p>
                    <p class="text-slate-500">NIP/NIY: {{ $nipKepala ?? '.........................................' }}</p>
                </div>

                <!-- Biro Pendidikan Yayasan -->
                <div class="space-y-1">
                    <p class="font-semibold text-slate-600">Menyetujui,</p>
                    <p class="font-bold text-slate-800 uppercase tracking-wide">Biro Pendidikan Yayasan</p>
                    <p class="font-bold text-slate-800 uppercase tracking-wide">Pendidikan Islam Syafiiyah</p>
                    <div class="h-14"></div>
                    <p class="font-bold text-slate-800 underline">{{ $kepalaBiro ?? '.........................................' }}</p>
                    <p class="text-slate-500">NIP/NIY: {{ $nipBiro ?? '.........................................' }}</p>
                </div>
            </div>
        </div>

        <div class="doc-footer flex justify-between items-center text-[10px] text-slate-400 mt-10 pt-4 border-t border-slate-100 font-medium">
            <p>MTs Syafiiyah Digital Report</p>
            <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }} WIB</p>
        </div>

    </div>

    <!-- Trigger browser print automatically -->
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
