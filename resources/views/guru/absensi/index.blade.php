@extends('layouts.app')

@section('title', 'Pencatatan Absensi Siswa - Pembelajaran Digital')
@section('breadcrumb', 'Absensi')
@section('page-title', 'Absensi Harian')

<style>
    /* Membuat option menjadi hitam */
    select option {
        color: black !important;
        background-color: white !important;
    }
    
    /* Untuk select yang terbuka di mobile */
    select optgroup {
        color: black !important;
        background-color: white !important;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('guru.absensi.index') }}" id="filterForm">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Pilih Jam Mengajar (Jadwal)</label>
                    <select name="jadwal_pelajaran_id" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">-- Pilih Jam Mengajar --</option>
                        @foreach($jadwals as $j)
                            <option value="{{ $j->id }}" class="text-black bg-white" {{ $selectedJadwalId == $j->id ? 'selected' : '' }}>
                                {{ $j->hari }}, {{ $j->jam_mulai }} - {{ $j->jam_selesai }} | {{ $j->mapel->nama_mapel }} - {{ $j->kelas->kode_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Tanggal Absensi</label>
                    <input type="date" name="tanggal" required max="{{ date('Y-m-d') }}"
                           value="{{ $selectedTanggal }}"
                           class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                </div>
                <div class="w-full md:w-auto shrink-0 flex gap-2">
                    <button type="submit" class="w-full md:w-auto px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg text-white font-semibold text-sm flex items-center justify-center gap-1.5">
                        <i class="fas fa-filter"></i> Tampilkan
                    </button>
                    @if($selectedJadwalId)
                        <a href="{{ route('guru.absensi.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400 flex items-center justify-between">
        <div class="text-sm md:text-base">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 flex items-center justify-between">
        <div class="text-sm md:text-base">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Main Entry Panel -->
    @if($selectedJadwalId && $selectedTanggal)
        @php
            $currentJadwal = \App\Models\JadwalPelajaran::with('kelas', 'mapel')->find($selectedJadwalId);
        @endphp
        <div class="luxury-card overflow-hidden">
            <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div>
                        <h6 class="text-white font-semibold text-lg">Pencatatan Kehadiran Harian</h6>
                        <p class="text-white/40 text-xs md:text-sm">
                            Kelas: <strong class="text-blue-300">{{ $currentJadwal->kelas->kode_kelas }}</strong> | 
                            Mata Pelajaran: <strong class="text-emerald-300">{{ $currentJadwal->mapel->nama_mapel }}</strong> |
                            Jam Mengajar: <strong class="text-yellow-300">{{ $currentJadwal->hari }} ({{ $currentJadwal->jam_mulai }} - {{ $currentJadwal->jam_selesai }})</strong> |
                            Tanggal: <strong class="text-purple-300">{{ \Carbon\Carbon::parse($selectedTanggal)->format('d M Y') }}</strong>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="showQrModal()" class="px-3.5 py-1.5 bg-purple-500/20 rounded-lg text-purple-400 text-xs font-semibold hover:bg-purple-500/30 transition flex items-center gap-1.5 border border-purple-500/20">
                            <i class="fas fa-qrcode"></i> QR Absensi
                        </button>
                        <a href="{{ route('guru.absensi.export', ['jadwal_pelajaran_id' => $selectedJadwalId, 'tanggal' => $selectedTanggal]) }}" class="px-3.5 py-1.5 bg-blue-500/20 rounded-lg text-blue-400 text-xs font-semibold hover:bg-blue-500/30 transition flex items-center gap-1.5">
                            <i class="fas fa-file-csv"></i> Ekspor Absensi (CSV)
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('guru.absensi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_pelajaran_id" value="{{ $selectedJadwalId }}">
                <input type="hidden" name="tanggal" value="{{ $selectedTanggal }}">

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[700px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">NIS</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                                <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-72">Status Kehadiran</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-40">Info Absen</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $index => $item)
                                @php
                                    $record = $records->get($item->id);
                                    $currentStatus = $record ? $record->status : 'H'; // Default: Hadir (H)
                                @endphp
                                <tr class="border-b border-white/5 hover:bg-white/5">
                                    <td class="p-3 md:p-4 text-white/80 text-sm">{{ $index + 1 }}</td>
                                    <td class="p-3 md:p-4">
                                        <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-500/20 text-purple-400">
                                            {{ $item->nis }}
                                        </span>
                                    </td>
                                    <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $item->nama }}</td>
                                    
                                    <!-- Radio Buttons Kehadiran -->
                                    <td class="p-3 md:p-4 text-center">
                                        <div class="inline-flex items-center gap-3 bg-white/5 border border-white/10 rounded-xl p-1.5">
                                            <label class="flex items-center gap-1 cursor-pointer px-2 py-0.5 rounded-lg transition hover:bg-white/5">
                                                <input type="radio" 
                                                       name="absensi[{{ $item->id }}][status]" 
                                                       value="H" 
                                                       class="text-purple-600 focus:ring-purple-500 bg-black/40 border-white/20"
                                                       {{ $currentStatus == 'H' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-emerald-400">Hadir</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer px-2 py-0.5 rounded-lg transition hover:bg-white/5">
                                                <input type="radio" 
                                                       name="absensi[{{ $item->id }}][status]" 
                                                       value="S" 
                                                       class="text-purple-600 focus:ring-purple-500 bg-black/40 border-white/20"
                                                       {{ $currentStatus == 'S' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-blue-400">Sakit</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer px-2 py-0.5 rounded-lg transition hover:bg-white/5">
                                                <input type="radio" 
                                                       name="absensi[{{ $item->id }}][status]" 
                                                       value="I" 
                                                       class="text-purple-600 focus:ring-purple-500 bg-black/40 border-white/20"
                                                       {{ $currentStatus == 'I' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-yellow-400">Izin</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer px-2 py-0.5 rounded-lg transition hover:bg-white/5">
                                                <input type="radio" 
                                                       name="absensi[{{ $item->id }}][status]" 
                                                       value="A" 
                                                       class="text-purple-600 focus:ring-purple-500 bg-black/40 border-white/20"
                                                       {{ $currentStatus == 'A' ? 'checked' : '' }}>
                                                <span class="text-xs font-bold text-red-400">Alpa</span>
                                            </label>
                                        </div>
                                    </td>
                                    
                                    <!-- Info Absen -->
                                    <td class="p-3 md:p-4 text-white/80 text-xs">
                                        @if($record)
                                            <span class="block font-semibold text-white/90">{{ $record->created_at ? $record->created_at->format('H:i') : '-' }} WIB</span>
                                            <span class="block text-white/40 text-[10px]">{{ $record->guru ? $record->guru->name : 'Sistem (QR Code)' }}</span>
                                        @else
                                            <span class="text-white/30 italic">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Input Keterangan -->
                                    <td class="p-3 md:p-4">
                                        <input type="text" 
                                               name="absensi[{{ $item->id }}][keterangan]" 
                                               value="{{ old('absensi.'.$item->id.'.keterangan', $record ? $record->keterangan : '') }}"
                                               placeholder="Catatan sakit, terlambat, dll."
                                               class="w-full px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-white text-xs focus:border-purple-500 focus:outline-none">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-12 text-center text-white/50">
                                        <i class="fas fa-users-slash text-5xl mb-3 text-white/10"></i>
                                        <p>Tidak ada siswa terdaftar di kelas ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswa->isNotEmpty())
                    <div class="p-4 md:p-6 border-t border-white/10 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg text-white font-bold text-sm shadow-glow flex items-center gap-1.5">
                            <i class="fas fa-save"></i> Simpan Absensi
                        </button>
                    </div>
                @endif
            </form>
        </div>

        <!-- ==================== QR CODE MODAL ==================== -->
        <div id="qrModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeQrModal()"></div>
            
            <!-- Modal Box -->
            <div class="relative bg-gradient-to-br from-slate-900 to-slate-950 border border-white/10 rounded-2xl w-full max-w-md p-6 overflow-hidden text-center bg-slate-950">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10 text-left">
                    <h5 class="text-white font-bold text-lg"><i class="fas fa-qrcode text-purple-400 mr-1.5"></i> QR Code Absensi Kelas</h5>
                    <button onclick="closeQrModal()" class="text-white/40 hover:text-white"><i class="fas fa-times"></i></button>
                </div>
                
                <p class="text-white/60 text-xs mb-4">
                    Minta siswa kelas <strong class="text-blue-300">{{ $currentJadwal->kelas->kode_kelas }}</strong> membuka dashboard HP mereka dan memindai QR Code di bawah untuk absensi otomatis mata pelajaran <strong class="text-emerald-300">{{ $currentJadwal->mapel->nama_mapel }}</strong> jam mengajar <strong>{{ $currentJadwal->hari }} ({{ $currentJadwal->jam_mulai }} - {{ $currentJadwal->jam_selesai }})</strong> tanggal <strong>{{ \Carbon\Carbon::parse($selectedTanggal)->format('d M Y') }}</strong>.
                </p>

                <!-- QR Code Canvas Area -->
                <div class="w-64 h-64 bg-white p-4 rounded-xl mx-auto flex items-center justify-center border border-white/10 shadow-[0_0_20px_rgba(168,85,247,0.3)]">
                    <canvas id="qrCodeCanvas"></canvas>
                </div>

                <div class="mt-4 p-3 bg-white/5 rounded-xl border border-white/5 text-xs text-white/50 space-y-1">
                    <p class="text-white/30 uppercase tracking-widest text-[9px]">KODE ABSENSI MANUAL</p>
                    <p class="text-lg font-bold text-purple-300 font-mono tracking-widest">{{ substr(md5($selectedKelasId.$selectedMapelId.$selectedTanggal), 0, 6) }}</p>
                </div>
            </div>
        </div>

        <!-- load Qrious QR code generator library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

        <script>
            function showQrModal() {
                document.getElementById('qrModal').style.display = 'flex';
                
                // Generate QR code using QRious
                const qrUrl = "{{ url('/siswa/absen-qr-code') }}?kelas_id={{ $selectedKelasId }}&mapel_id={{ $selectedMapelId }}&jadwal_pelajaran_id={{ $selectedJadwalId }}&tanggal={{ $selectedTanggal }}&token={{ md5($selectedKelasId.$selectedMapelId.$selectedTanggal) }}&guru_id={{ auth()->id() }}";
                
                new QRious({
                    element: document.getElementById('qrCodeCanvas'),
                    value: qrUrl,
                    size: 220,
                    background: '#ffffff',
                    foreground: '#000000',
                    level: 'H'
                });
            }

            function closeQrModal() {
                document.getElementById('qrModal').style.display = 'none';
            }
        </script>
    @else
        <!-- Welcome / Instructions Panel -->
        <div class="luxury-card p-8 md:p-12 text-center text-white/80">
            <div class="max-w-md mx-auto space-y-4">
                <div class="luxury-icon w-16 h-16 mx-auto bg-purple-500/20 flex items-center justify-center text-purple-400">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white tracking-tight">Pencatatan Absensi Harian</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Silakan gunakan panel filter di atas untuk memilih **Jam Mengajar (Jadwal)** dan **Tanggal** absensi yang ingin Anda catat.
                </p>
                <div class="p-4 bg-white/5 rounded-xl border border-white/10 text-left text-xs space-y-2">
                    <p class="font-semibold text-purple-400"><i class="fas fa-info-circle"></i> Info Absensi:</p>
                    <ul class="list-disc list-inside space-y-1 text-white/60 pl-1">
                        <li>Pilihan default adalah <strong>Hadir</strong> untuk semua siswa.</li>
                        <li>Sertakan keterangan (seperti alasan sakit/izin) di kolom keterangan.</li>
                        <li>Pencatatan absensi di masa depan tidak diizinkan.</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
