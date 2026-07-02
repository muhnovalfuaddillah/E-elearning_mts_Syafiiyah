<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Jurnal Mengajar - Administrator</title>
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
            }
            @page {
                size: A4 landscape;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body class="p-4 md:p-8 print-padding">

    <!-- Floating Action Buttons (No Print) -->
    <div class="max-w-7xl mx-auto mb-6 no-print flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <a href="{{ route('admin.jurnal.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-lg shadow-blue-500/20 transition-all hover:scale-[1.02]">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Main Document Container -->
    <div class="max-w-7xl mx-auto bg-white p-8 md:p-12 rounded-3xl shadow-xl shadow-slate-100 border border-slate-100 document-card">
        
        <!-- ==================== KOP SURAT RESMI (LETTERHEAD) ==================== -->
        <div class="flex items-center justify-between pb-4 border-b-4 border-double border-slate-900 gap-4 mb-6">
            <!-- Logo Madrasah -->
            <div class="w-20 h-20 bg-slate-100 rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 text-3xl font-bold shrink-0">
                <i class="fas fa-school text-blue-600"></i>
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

        <!-- Document Title -->
        <div class="text-center mb-6">
            <h3 class="font-extrabold text-base md:text-lg uppercase tracking-wider text-slate-800 underline">Laporan Rekapitulasi Jurnal Mengajar Guru</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Tahun Ajaran 2025/2026</p>
        </div>

        <!-- Metadata Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 bg-slate-50 p-5 rounded-2xl border border-slate-100">
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Pendidik</span>
                <p class="text-slate-800 font-bold text-sm">{{ $guruFilter ? $guruFilter->name : 'Semua Guru' }}</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Kelas</span>
                <p class="text-blue-600 font-bold text-sm">{{ $kelasFilter ? $kelasFilter->nama_lengkap : 'Semua Kelas' }}</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Mata Pelajaran</span>
                <p class="text-slate-800 font-bold text-sm">{{ $mapelFilter ? $mapelFilter->nama_mapel : 'Semua Mapel' }}</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Periode</span>
                <p class="text-slate-700 font-semibold text-xs leading-tight">
                    @if($tanggalMulai && $tanggalSelesai)
                        {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                    @elseif($tanggalMulai)
                        Mulai {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }}
                    @elseif($tanggalSelesai)
                        Sampai {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                    @else
                        Semua Riwayat
                    @endif
                </p>
            </div>
        </div>

        <!-- Jurnal Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-100 mb-6">
            <table class="w-full text-xs md:text-sm">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="p-3.5 text-center font-bold w-12">No</th>
                        <th class="p-3.5 text-center font-bold w-24">Tanggal</th>
                        <th class="p-3.5 text-left font-bold w-48">Pendidik</th>
                        <th class="p-3.5 text-center font-bold w-20">Kelas</th>
                        <th class="p-3.5 text-center font-bold w-16">Pert.</th>
                        <th class="p-3.5 text-left font-bold w-48">Mata Pelajaran</th>
                        <th class="p-3.5 text-left font-bold">Materi Pokok & Kegiatan</th>
                        <th class="p-3.5 text-left font-bold w-44">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jurnals as $index => $item)
                        <tr class="hover:bg-slate-50 transition-colors {{ $index % 2 === 1 ? 'bg-slate-50/50' : '' }}">
                            <td class="p-3.5 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="p-3.5 text-center font-semibold text-slate-700">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="p-3.5 font-bold text-slate-800">{{ $item->guru->name }}</td>
                            <td class="p-3.5 text-center font-bold text-blue-600">{{ $item->kelas->kode_kelas }}</td>
                            <td class="p-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 font-bold text-[10px]">
                                    Ke-{{ $item->pertemuan_ke }}
                                </span>
                            </td>
                            <td class="p-3.5 font-medium text-slate-700">{{ $item->mapel->nama_mapel }}</td>
                            <td class="p-3.5 space-y-1 leading-relaxed">
                                <div>
                                    <span class="text-[9px] uppercase font-bold text-blue-500 tracking-wide">Materi Pokok</span>
                                    <p class="text-slate-700 font-semibold text-[11px]">{{ $item->materi }}</p>
                                </div>
                                <div class="pt-1 border-t border-slate-100">
                                    <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wide">Kegiatan</span>
                                    <p class="text-slate-600 text-[11px]">{{ $item->kegiatan }}</p>
                                </div>
                            </td>
                            <td class="p-3.5 text-[11px] leading-relaxed text-slate-500 italic">
                                {{ $item->catatan ? $item->catatan : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400 italic">
                                <i class="fas fa-folder-open text-4xl mb-2 text-slate-200 block"></i>
                                Tidak ada data jurnal mengajar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center text-[10px] text-slate-400 mt-8 pt-4 border-t border-slate-100 font-medium">
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
