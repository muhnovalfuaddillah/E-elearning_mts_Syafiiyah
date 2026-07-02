@extends('layouts.app')

@section('title', 'Daftar Kelas - MTs Syafiiyah')
@section('breadcrumb', 'Daftar Kelas')
@section('page-title', 'Daftar Kelas')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Header info banner -->
    <div class="luxury-card p-6 mb-8 flex items-center gap-4">
        <div class="luxury-icon w-12 h-12">
            <i class="fas fa-school text-white text-xl"></i>
        </div>
        <div>
            <h4 class="text-white font-bold text-lg">Direktori Kelas</h4>
            <p class="text-white/50 text-sm">Lihat semua kelas aktif dan telusuri daftar siswanya.</p>
        </div>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($kelas as $item)
            <div class="luxury-card flex flex-col justify-between overflow-hidden group">
                <div class="p-6">
                    <!-- Class Header & Level Badge -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                Kelas {{ $item->tingkat == '10' || $item->tingkat == 'X' ? 'X' : ($item->tingkat == '11' || $item->tingkat == 'XI' ? 'XI' : ($item->tingkat == '12' || $item->tingkat == 'XII' ? 'XII' : $item->tingkat)) }}
                            </span>
                        </div>
                        <span class="text-white/30 text-xs font-mono">
                            {{ $item->kode_kelas }}
                        </span>
                    </div>

                    <!-- Class Name and Info -->
                    <h5 class="text-white font-bold text-xl mb-1 group-hover:text-blue-400 transition-colors">
                        {{ $item->kode_kelas }}
                    </h5>
                    <p class="text-white/50 text-sm mb-3">
                        Jurusan: {{ $item->jurusan }}
                    </p>
                    
                    @if($item->deskripsi)
                        <p class="text-white/40 text-xs line-clamp-2">
                            {{ $item->deskripsi }}
                        </p>
                    @endif
                </div>

                <!-- Footer details with Student Count and Link -->
                <div class="px-6 py-4 bg-white/5 border-t border-white/5 flex justify-between items-center">
                    <span class="text-white/60 text-sm">
                        <i class="fas fa-users text-blue-400 mr-1.5"></i>
                        <strong>{{ $item->siswa_count }}</strong> Siswa
                    </span>
                    <a href="{{ route('guru.kelas.siswa', $item->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-400 group-hover:text-emerald-300 transition-colors">
                        Lihat Siswa <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full luxury-card p-10 text-center text-white/40">
                <i class="fas fa-school text-5xl mb-3 block text-white/10"></i>
                Belum ada data kelas yang terdaftar.
            </div>
        @endforelse
    </div>

</div>
@endsection

