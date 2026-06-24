@extends('layouts.app')

@section('title', 'Pengumuman - MTs Syafiiyah')
@section('breadcrumb', 'Pengumuman')
@section('page-title', 'Pengumuman')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Header Stats Banner -->
    <div class="luxury-card p-6 mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="luxury-icon w-12 h-12 shrink-0">
                <i class="fas fa-bullhorn text-white text-xl animate-bounce"></i>
            </div>
            <div>
                <h4 class="text-white font-bold text-lg">Papan Informasi & Pengumuman</h4>
                <p class="text-white/50 text-sm">Berikut adalah pengumuman sekolah dan pengumuman kelas Anda.</p>
            </div>
        </div>
        <div class="px-4 py-2 bg-purple-500/10 border border-purple-500/20 rounded-xl text-purple-400 text-sm font-semibold shrink-0">
            Kelas Anda: {{ auth()->user()->siswa->kelas->kode_kelas }}
        </div>
    </div>

    <!-- Announcement Board Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($pengumuman as $item)
            <div class="luxury-card flex flex-col justify-between overflow-hidden group">
                
                <!-- Inner Card Content -->
                <div class="p-6">
                    <!-- Category Badge and Time -->
                    <div class="flex justify-between items-center mb-4">
                        @if($item->tipe === 'sekolah')
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <i class="fas fa-globe mr-1"></i> Sekolah
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-pink-500/20 text-pink-400 border border-pink-500/30">
                                <i class="fas fa-graduation-cap mr-1"></i> Kelas
                            </span>
                        @endif
                        <span class="text-white/40 text-xs flex items-center gap-1.5">
                            <i class="far fa-clock"></i>
                            {{ $item->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h5 class="text-white font-bold text-lg mb-2 group-hover:text-purple-400 transition-colors line-clamp-1">
                        {{ $item->judul }}
                    </h5>

                    <!-- Short Content Preview -->
                    <p class="text-white/60 text-sm leading-relaxed mb-4 line-clamp-3">
                        {{ strip_tags($item->isi) }}
                    </p>
                </div>

                <!-- Action / Bottom bar -->
                <div class="px-6 py-4 bg-white/5 border-t border-white/5 flex justify-between items-center">
                    <span class="text-white/40 text-xs">
                        Oleh: <strong class="text-white/60 font-medium">{{ $item->user->name }}</strong>
                    </span>
                    <a href="{{ route('announcements.show-detail', $item->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-400 group-hover:text-purple-300 transition-all">
                        Baca Selengkapnya <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>

            </div>
        @empty
            <div class="col-span-full luxury-card p-10 text-center text-white/40">
                <i class="fas fa-bullhorn text-5xl mb-4 block text-white/20 animate-pulse"></i>
                <h5 class="text-white font-semibold text-lg mb-1">Belum Ada Pengumuman</h5>
                <p class="text-white/40 text-sm">Tidak ada pengumuman sekolah atau kelas untuk Anda saat ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Paginasi -->
    @if($pengumuman->hasPages())
        <div class="mt-8">
            {{ $pengumuman->links() }}
        </div>
    @endif

</div>
@endsection

