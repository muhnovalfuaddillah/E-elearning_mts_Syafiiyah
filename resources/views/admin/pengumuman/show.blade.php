@extends('layouts.app')

@section('title', 'Detail Pengumuman - MTs Syafiiyah')
@section('breadcrumb', 'Detail Pengumuman')
@section('page-title', 'Detail Pengumuman')

@section('content')
<div class="w-full px-4 md:px-6 py-6 flex justify-center">
    <div class="w-full max-w-3xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route(auth()->user()->role . '.pengumuman.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white transition-colors text-sm font-semibold">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pengumuman
            </a>
        </div>

        <!-- Announcement Card -->
        <div class="luxury-card overflow-hidden">
            <!-- Header -->
            <div class="p-6 md:p-8 border-b border-white/10 bg-white/5 relative">
                <div class="absolute top-6 right-6">
                    @if($pengumuman->tipe === 'sekolah')
                        <span class="px-3 py-1 rounded-xl text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            Sekolah
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-xl text-xs font-semibold bg-teal-500/20 text-teal-400 border border-teal-500/30">
                            Kelas: {{ $pengumuman->kelas->kode_kelas }}
                        </span>
                    @endif
                </div>

                <h2 class="text-white text-xl md:text-3xl font-extrabold tracking-tight mt-2 pr-20">
                    {{ $pengumuman->judul }}
                </h2>

                <div class="flex flex-wrap gap-4 items-center mt-4 text-white/50 text-xs md:text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-400"></i>
                        <span>Diposting oleh: <strong class="text-white/80">{{ $pengumuman->user->name }}</strong></span>
                    </div>
                    <div class="w-1.5 h-1.5 rounded-full bg-white/20"></div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-day text-blue-400"></i>
                        <span>{{ $pengumuman->created_at->format('d F Y - H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Body Content -->
            <div class="p-6 md:p-8 text-white/90 text-sm md:text-base leading-relaxed whitespace-pre-line border-b border-white/5 bg-black/10">
                {!! nl2br(e($pengumuman->isi)) !!}
            </div>

            <!-- Footer info -->
            <div class="p-4 px-6 md:px-8 bg-white/5 text-white/40 text-xs flex justify-between items-center">
                <span>MTs Syafiiyah Digital Learning</span>
                <span>ID: P-{{ str_pad($pengumuman->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

    </div>
</div>
@endsection

