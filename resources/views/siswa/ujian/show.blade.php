@extends('layouts.app')

@section('title', 'Detail Ujian - Pembelajaran Digital')
@section('breadcrumb', 'Detail Ujian')
@section('page-title', 'Konfirmasi Ruang Ujian')

@section('content')
<div class="w-full px-4 md:px-6 py-6 text-white">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('siswa.ujian.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-6 text-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
        </a>

        <!-- Alert Messages -->
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
            <!-- Header Card -->
            <div class="p-6 border-b border-white/10 bg-gradient-to-br from-blue-600/10 to-indigo-600/5">
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                        {{ $ujian->mapel->kode_mapel ?? 'MAPEL' }}
                    </span>
                    <span class="text-white/40 text-xs">Oleh: {{ $ujian->guru->name }}</span>
                </div>
                <h4 class="text-white font-bold text-xl md:text-2xl leading-snug">{{ $ujian->judul }}</h4>
            </div>

            <!-- Detail & Informasi -->
            <div class="p-6 space-y-6">
                <!-- Deskripsi / Tata Tertib -->
                <div>
                    <h6 class="text-xs uppercase tracking-wider text-white/40 font-bold mb-2">Petunjuk & Tata Tertib Ujian:</h6>
                    <div class="p-4 bg-white/5 rounded-xl border border-white/5 text-sm leading-relaxed text-white/80 whitespace-pre-line">
                        {{ $ujian->deskripsi ?? 'Tidak ada petunjuk khusus. Kerjakan dengan jujur dan teliti.' }}
                    </div>
                </div>

                <!-- Spesifikasi Ujian -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-white/5 rounded-xl border border-white/5 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                            <i class="far fa-clock text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-white/40 font-bold">Durasi</p>
                            <p class="text-sm font-bold text-white">{{ $ujian->durasi }} Menit</p>
                        </div>
                    </div>

                    <div class="p-4 bg-white/5 rounded-xl border border-white/5 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                            <i class="fas fa-list-ol text-lg"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-white/40 font-bold font-semibold">Jumlah Soal</p>
                            <p class="text-sm font-bold text-white">{{ $jumlahSoal }} Soal PG</p>
                        </div>
                    </div>
                </div>

                <!-- Info Waktu -->
                <div class="p-4 bg-yellow-500/5 border border-yellow-500/10 rounded-xl space-y-2 text-xs text-yellow-400/80">
                    <p class="flex justify-between">
                        <span>Batas Mulai Ujian:</span>
                        <span class="font-semibold text-white">{{ $ujian->waktu_mulai->format('d M Y, H:i') }} WIB</span>
                    </p>
                    <p class="flex justify-between">
                        <span>Batas Selesai Ujian:</span>
                        <span class="font-semibold text-white">{{ $ujian->waktu_selesai->format('d M Y, H:i') }} WIB</span>
                    </p>
                    <div class="pt-2 border-t border-white/5 text-[11px] text-white/50 leading-relaxed">
                        <i class="fas fa-info-circle text-blue-400 mr-1"></i> Ujian ini dilengkapi dengan <strong>auto-submit</strong>. Jika durasi habis atau Anda melewati batas waktu pengerjaan, lembar ujian akan dikirim secara otomatis ke server.
                    </div>
                </div>

                <!-- Form Mulai -->
                @if(!$sesi)
                    <form action="{{ route('siswa.ujian.mulai', $ujian->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memulai ujian sekarang? Durasi waktu pengerjaan akan langsung berjalan mundur.')">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 rounded-xl text-white font-bold text-sm tracking-wide transition flex items-center justify-center gap-2 shadow-glow">
                            <i class="fas fa-play"></i> MULAI KERJAKAN SEKARANG
                        </button>
                    </form>
                @elseif($sesi->status == 'mengerjakan')
                    <a href="{{ route('siswa.ujian.kerjakan', $ujian->id) }}" class="w-full py-3 bg-gradient-to-r from-yellow-500 to-amber-600 hover:opacity-90 rounded-xl text-white font-bold text-sm tracking-wide transition flex items-center justify-center gap-2 shadow-glow">
                        <i class="fas fa-play"></i> LANJUTKAN MENGERJAKAN
                    </a>
                @else
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-center text-emerald-400 text-sm font-semibold">
                        <i class="fas fa-check-double mr-1"></i> Anda telah menyelesaikan ujian ini. Nilai Anda: {{ number_format($sesi->nilai, 1) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
