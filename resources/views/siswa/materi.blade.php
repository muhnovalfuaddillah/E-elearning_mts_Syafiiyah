@extends('layouts.app')

@section('title', 'Materi Pelajaran - Pembelajaran Digital')
@section('breadcrumb', 'Materi')
@section('page-title', 'Materi Pelajaran Kelas')

@section('content')
<div class="w-full px-4 md:px-6 py-6">
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
            <h6 class="text-white font-semibold text-lg">Materi Pelajaran Tersedia</h6>
            <p class="text-white/40 text-xs md:text-sm">Unduh materi pelajaran kelas Anda untuk dipelajari secara mandiri.</p>
        </div>

        <div class="p-6">
            @if($materi->isEmpty())
                <div class="py-16 text-center text-white/50">
                    <i class="fas fa-folder-open text-6xl text-white/10 mb-4"></i>
                    <p class="text-sm">Belum ada materi pelajaran yang diunggah untuk kelas Anda saat ini.</p>
                </div>
            @else
                @php
                    $groupedMateri = $materi->groupBy(function($item) {
                        return $item->mapel->nama_mapel ?? 'Lainnya';
                    });
                @endphp

                @foreach($groupedMateri as $mapelName => $items)
                    <div class="mb-10 last:mb-0">
                        <!-- Header Mapel -->
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-1.5 h-6 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full"></span>
                            <h5 class="text-white font-bold text-lg tracking-wide">{{ $mapelName }}</h5>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-white/60">
                                {{ $items->count() }} Materi
                            </span>
                        </div>

                        <!-- Grid Materi per Mapel -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($items as $item)
                                @php
                                    $fileExtension = $item->file_path ? pathinfo($item->file_path, PATHINFO_EXTENSION) : null;
                                    $iconClass = 'fa-file-alt text-purple-400';
                                    $iconBg = 'bg-purple-500/10 border-purple-500/30';
                                    
                                    if ($item->tipe === 'pdf') {
                                        $iconClass = 'fa-file-pdf text-red-400';
                                        $iconBg = 'bg-red-500/10 border-red-500/30';
                                    } elseif ($item->tipe === 'ppt') {
                                        $iconClass = 'fa-file-powerpoint text-orange-400';
                                        $iconBg = 'bg-orange-500/10 border-orange-500/30';
                                    } elseif ($item->tipe === 'video') {
                                        $iconClass = 'fa-video text-blue-400';
                                        $iconBg = 'bg-blue-500/10 border-blue-500/30';
                                    }
                                @endphp
                                <div class="luxury-card p-5 hover:border-purple-500/30 transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="luxury-icon w-10 h-10 shrink-0 {{ $iconBg }} flex items-center justify-center">
                                                <i class="fas {{ $iconClass }} text-lg"></i>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                                {{ $item->mapel->kode_mapel ?? 'MAPEL' }}
                                            </span>
                                        </div>
                                        <h6 class="text-white font-bold text-base mt-4 leading-snug">{{ $item->judul }}</h6>
                                        <p class="text-white/50 text-xs mt-2 leading-relaxed line-clamp-3">{{ $item->deskripsi }}</p>
                                    </div>

                                    <div class="mt-6 pt-4 border-t border-white/5">
                                        <div class="flex items-center justify-between text-xs text-white/40 mb-4">
                                            <span class="truncate pr-2"><i class="fas fa-chalkboard-teacher text-purple-400/80 mr-1"></i> {{ $item->guru->name ?? 'Guru Pengampu' }}</span>
                                            <span class="shrink-0"><i class="far fa-calendar-alt text-purple-400/80 mr-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</span>
                                        </div>
                                        
                                        <div class="flex flex-col gap-2">
                                            @if($item->file_path)
                                                <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" download class="w-full py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-400 rounded-lg font-semibold text-xs transition flex items-center justify-center gap-1.5 border border-purple-500/20">
                                                    <i class="fas fa-download"></i> Unduh Materi (.{{ $fileExtension }})
                                                </a>
                                            @endif
                                            
                                            @if($item->link_video)
                                                <a href="{{ $item->link_video }}" target="_blank" class="w-full py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg font-semibold text-xs transition flex items-center justify-center gap-1.5 border border-blue-500/20">
                                                    <i class="fas fa-external-link-alt"></i> Tonton Video
                                                </a>
                                            @endif
                                            
                                            @if(!$item->file_path && !$item->link_video)
                                                <div class="text-center py-2 text-white/30 text-xs italic">
                                                    Materi Teks Deskriptif
                                                </div>
                                            @endif
                                        </div>
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
