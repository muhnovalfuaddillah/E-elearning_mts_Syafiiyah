@extends('layouts.app')

@section('title', 'Hasil Ujian - Pembelajaran Digital')
@section('breadcrumb', 'Hasil Ujian')
@section('page-title', 'Hasil Ujian Anda')

@section('content')
<div class="w-full px-4 md:px-6 py-6 text-white">
    <div class="max-w-xl mx-auto">
        
        <!-- Top Back Link -->
        <a href="{{ route('siswa.ujian.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-6 text-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
        </a>

        <!-- Success Message Alert -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400 flex items-center justify-between">
            <div class="text-sm">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <!-- Main Card -->
        <div class="luxury-card overflow-hidden text-center">
            <!-- Top Gradient Header -->
            <div class="p-8 border-b border-white/10 bg-gradient-to-br from-blue-600/10 to-indigo-600/5 flex flex-col items-center">
                <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-2xl mb-4 shadow-glow">
                    <i class="fas fa-trophy"></i>
                </div>
                <h4 class="text-white font-bold text-xl leading-snug">{{ $ujian->judul }}</h4>
                <p class="text-white/40 text-xs mt-1">Mata Pelajaran: {{ $ujian->mapel->nama_mapel }}</p>
            </div>

            <!-- Score Dashboard -->
            <div class="p-8 space-y-6">
                
                <div>
                    <p class="text-xs uppercase tracking-wider text-white/40 font-bold mb-2">Nilai Akhir Ujian</p>
                    <div class="inline-block px-8 py-5 bg-white/5 border border-white/10 rounded-2xl">
                        <span class="text-5xl font-extrabold tracking-tight {{ $sesi->nilai >= 75 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ number_format($sesi->nilai, 1) }}
                        </span>
                        <span class="text-white/30 text-lg font-medium">/ 100</span>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3.5 bg-white/5 border border-white/5 rounded-xl">
                        <p class="text-[10px] uppercase text-white/45 font-semibold">Total Soal</p>
                        <p class="text-lg font-bold text-white mt-1">{{ $totalSoal }}</p>
                    </div>
                    <div class="p-3.5 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
                        <p class="text-[10px] uppercase text-emerald-500/50 font-semibold">Jawaban Benar</p>
                        <p class="text-lg font-bold text-emerald-400 mt-1">{{ $totalBenar }}</p>
                    </div>
                    <div class="p-3.5 bg-red-500/5 border border-red-500/10 rounded-xl">
                        <p class="text-[10px] uppercase text-red-500/50 font-semibold font-semibold">Jawaban Salah</p>
                        <p class="text-lg font-bold text-red-400 mt-1">{{ $totalSalah }}</p>
                    </div>
                </div>

                <!-- Session Meta -->
                <div class="p-4 bg-white/5 rounded-xl border border-white/5 text-xs text-left space-y-2 text-white/60">
                    <div class="flex justify-between">
                        <span>Waktu Mulai:</span>
                        <span class="text-white font-medium">{{ $sesi->waktu_mulai->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Waktu Selesai:</span>
                        <span class="text-white font-medium">{{ $sesi->waktu_selesai->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Durasi Pengerjaan:</span>
                        <span class="text-blue-400 font-semibold">
                            {{ $sesi->waktu_mulai->diffInMinutes($sesi->waktu_selesai) }} Menit
                        </span>
                    </div>
                </div>

                <a href="{{ route('siswa.ujian.index') }}" class="block w-full py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-white font-bold text-xs tracking-wider transition">
                    KEMBALI KE DAFTAR UJIAN
                </a>
            </div>
        </div>
        
    </div>
</div>
@endsection
