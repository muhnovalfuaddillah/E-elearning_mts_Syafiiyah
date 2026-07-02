@extends('layouts.app')

@section('title', 'Ujian Online - Pembelajaran Digital')
@section('breadcrumb', 'Ujian')
@section('page-title', 'Ujian Online')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-400 flex items-center justify-between">
        <div class="text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-emerald-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 flex items-center justify-between">
        <div class="text-sm">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
            <h6 class="text-white font-semibold text-lg">Daftar Ujian Online Anda</h6>
            <p class="text-white/40 text-xs md:text-sm">Tabel ini menampilkan daftar ujian mandiri yang ditujukan untuk kelas Anda.</p>
        </div>

        <div class="p-6">
            @if($ujians->isEmpty())
                <div class="py-16 text-center text-white/50">
                    <i class="fas fa-file-signature text-6xl text-white/10 mb-4"></i>
                    <p class="text-sm">Belum ada ujian online yang diterbitkan untuk kelas Anda.</p>
                </div>
            @else
                @php
                    $groupedUjian = $ujians->groupBy(function($item) {
                        return $item->mapel->nama_mapel ?? 'Lainnya';
                    });
                @endphp

                @foreach($groupedUjian as $mapelName => $items)
                    <div class="mb-10 last:mb-0">
                        <!-- Header Mapel -->
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-1.5 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></span>
                            <h5 class="text-white font-bold text-lg tracking-wide">{{ $mapelName }}</h5>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-white/60">
                                {{ $items->count() }} Ujian
                            </span>
                        </div>

                        <!-- Grid Ujian per Mapel -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($items as $item)
                                @php
                                    $sesi = $ujianSiswas->get($item->id);
                                    $hasFinished = $sesi && $sesi->status === 'selesai';
                                    $isWorking = $sesi && $sesi->status === 'mengerjakan';
                                    
                                    $now = \Carbon\Carbon::now();
                                    $notStartedYet = $now->lessThan($item->waktu_mulai);
                                    $alreadyEnded = $now->greaterThan($item->waktu_selesai);
                                    $isOpen = !$notStartedYet && !$alreadyEnded && $item->status === 'published';
                                @endphp
                                <div class="luxury-card p-5 hover:border-blue-500/30 transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start gap-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                                {{ $item->mapel->kode_mapel ?? 'MAPEL' }}
                                            </span>
                                            
                                            @if($hasFinished)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                    Selesai
                                                </span>
                                            @elseif($isWorking)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 animate-pulse">
                                                    Sedang Dikerjakan
                                                </span>
                                            @elseif($notStartedYet)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-white/10 text-white/50 border border-white/5">
                                                    Belum Mulai
                                                </span>
                                            @elseif($alreadyEnded)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">
                                                    Berakhir
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                                    Tersedia
                                                </span>
                                            @endif
                                        </div>

                                        <h6 class="text-white font-bold text-base mt-4 leading-snug">{{ $item->judul }}</h6>
                                        <p class="text-white/50 text-xs mt-2 leading-relaxed line-clamp-2">{{ $item->deskripsi ?? 'Tidak ada deskripsi/petunjuk pengerjaan.' }}</p>

                                        <div class="mt-4 p-3 bg-white/5 rounded-xl border border-white/5 space-y-1 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-white/40">Jadwal Ujian:</span>
                                                <span class="text-white/80 font-medium">{{ $item->waktu_mulai->format('d M Y, H:i') }} - {{ $item->waktu_selesai->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-white/40">Durasi Pengerjaan:</span>
                                                <span class="text-blue-400 font-semibold">{{ $item->durasi }} Menit</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-white/40">Jumlah Soal:</span>
                                                <span class="text-white/80 font-medium">{{ $item->soals()->count() }} Butir Pilihan Ganda</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Panel Tindakan -->
                                    <div class="mt-6 pt-4 border-t border-white/5">
                                        @if($hasFinished)
                                            <div class="flex items-center justify-between p-3 bg-emerald-500/5 border border-emerald-500/10 rounded-xl mb-3">
                                                <span class="text-white/50 text-xs">Nilai Ujian Anda:</span>
                                                <span class="text-emerald-400 font-bold text-base">{{ number_format($sesi->nilai, 1) }}</span>
                                            </div>
                                            <a href="{{ route('siswa.ujian.hasil', $item->id) }}" class="w-full py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white font-semibold text-xs transition flex items-center justify-center gap-1.5">
                                                <i class="fas fa-poll-h"></i> Lihat Detail Hasil
                                            </a>
                                        @elseif($isWorking)
                                            <a href="{{ route('siswa.ujian.kerjakan', $item->id) }}" class="w-full py-2 bg-gradient-to-r from-yellow-500 to-amber-600 hover:opacity-90 rounded-lg text-white font-semibold text-xs transition flex items-center justify-center gap-1.5 shadow-glow">
                                                <i class="fas fa-play"></i> Lanjutkan Mengerjakan
                                            </a>
                                        @elseif($isOpen)
                                            <a href="{{ route('siswa.ujian.show', $item->id) }}" class="w-full py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 rounded-lg text-white font-semibold text-xs transition flex items-center justify-center gap-1.5 shadow-glow">
                                                <i class="fas fa-sign-in-alt"></i> Masuk Ruang Ujian
                                            </a>
                                        @else
                                            <button disabled class="w-full py-2 bg-white/5 border border-white/5 rounded-lg text-white/35 font-semibold text-xs transition flex items-center justify-center gap-1.5 cursor-not-allowed">
                                                <i class="fas fa-lock"></i> Ujian Tidak Tersedia
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
