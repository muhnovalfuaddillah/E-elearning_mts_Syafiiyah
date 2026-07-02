@extends('layouts.app')

@section('title', 'Kehadiran Saya - Pembelajaran Digital')
@section('breadcrumb', 'Absensi')
@section('page-title', 'Kehadiran Saya')

@section('content')
<div class="w-full px-4 md:px-6 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Tabel Histori Absensi -->
        <div class="lg:col-span-2 space-y-6">
            <div class="luxury-card overflow-hidden">
                <div class="p-4 md:p-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
                    <div>
                        <h6 class="text-white font-semibold text-lg">Histori Kehadiran Harian</h6>
                        <p class="text-white/40 text-xs md:text-sm">Riwayat kehadiran harian Anda yang dicatat oleh guru kelas.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[500px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Jam Mengajar</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-28">Jam Absen</th>
                                <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Status Kehadiran</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Guru Pencatat</th>
                                <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensi as $index => $item)
                                <tr class="border-b border-white/5 hover:bg-white/5">
                                    <td class="p-3 md:p-4 text-white/80 text-sm">{{ $absensi->firstItem() + $index }}</td>
                                    <td class="p-3 md:p-4 text-white font-semibold text-sm">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                        <span class="block text-[10px] text-white/40 font-normal mt-0.5">{{ \Carbon\Carbon::parse($item->tanggal)->dayName }}</span>
                                    </td>
                                    <td class="p-3 md:p-4 text-white font-semibold text-sm">
                                        {{ $item->mapel ? $item->mapel->nama_mapel : '-' }}
                                        <span class="block text-[10px] text-white/40 font-normal mt-0.5">{{ $item->mapel ? $item->mapel->kode_mapel : '-' }}</span>
                                    </td>
                                    <td class="p-3 md:p-4 text-white/80 text-sm">
                                        @if($item->jadwalPelajaran)
                                            {{ $item->jadwalPelajaran->hari }} ({{ $item->jadwalPelajaran->jam_mulai }} - {{ $item->jadwalPelajaran->jam_selesai }})
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-3 md:p-4 text-white/80 text-sm">
                                        {{ $item->created_at ? $item->created_at->format('H:i') : '-' }} WIB
                                    </td>
                                    <td class="p-3 md:p-4 text-center text-sm">
                                        @if($item->status == 'H')
                                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">Hadir</span>
                                        @elseif($item->status == 'S')
                                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">Sakit</span>
                                        @elseif($item->status == 'I')
                                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Izin</span>
                                        @elseif($item->status == 'A')
                                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30">Alpa</span>
                                        @endif
                                    </td>
                                    <td class="p-3 md:p-4 text-white/80 text-sm">
                                        {{ $item->guru ? $item->guru->name : 'Sistem (QR Code)' }}
                                    </td>
                                    <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-12 text-center text-white/50">
                                        <i class="fas fa-calendar-times text-5xl mb-3 text-white/10"></i>
                                        <p>Belum ada rekaman histori kehadiran yang terdaftar.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($absensi->hasPages())
                    <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
                        <p class="text-white/40 text-xs">
                            Showing {{ $absensi->firstItem() }} to {{ $absensi->lastItem() }} of {{ $absensi->total() }} entries
                        </p>
                        <div class="flex gap-1">
                            {{ $absensi->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sisi Kanan: Persentase Kehadiran & Card QR Code Scan -->
        <div class="space-y-6">
            <!-- Presentase Card -->
            <div class="luxury-card p-6 text-center">
                <h5 class="text-white font-bold text-base mb-4 pb-2 border-b border-white/10 text-left">
                    <i class="fas fa-percent text-blue-400"></i> Rangkuman Persentase
                </h5>
                <div class="inline-flex items-center justify-center relative w-32 h-32 my-4">
                    <!-- Progress circle (CSS simulated) -->
                    <div class="absolute inset-0 rounded-full border-8 border-white/5"></div>
                    <div class="absolute inset-0 rounded-full border-8 border-transparent border-t-emerald-500 border-r-emerald-500 rotate-45"></div>
                    <div class="relative z-10 text-center">
                        <span class="text-2xl font-extrabold text-white">{{ number_format($persentase, 1) }}%</span>
                        <p class="text-[9px] text-white/40 uppercase tracking-widest mt-1">Kehadiran</p>
                    </div>
                </div>
                
                <p class="text-white/50 text-xs leading-relaxed px-2">
                    Kehadiran Anda di bawah 75% dapat memicu notifikasi WhatsApp peringatan otomatis kepada orang tua.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
