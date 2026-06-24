@extends('layouts.app')

@section('title', 'Siswa Kelas - MTs Syafiiyah')
@section('breadcrumb', 'Siswa Kelas')
@section('page-title', 'Siswa Kelas')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Back link and Header card -->
    <div class="mb-6">
        <a href="{{ route('guru.kelas.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white transition-colors text-sm font-semibold">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kelas
        </a>
    </div>

    <!-- Header info banner -->
    <div class="luxury-card p-6 mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="luxury-icon w-12 h-12">
                <i class="fas fa-graduation-cap text-white text-xl"></i>
            </div>
            <div>
                <h4 class="text-white font-bold text-lg">Daftar Siswa {{ $kelasItem->kode_kelas }}</h4>
                <p class="text-white/50 text-sm">Menampilkan semua profil siswa di kelas ini.</p>
            </div>
        </div>
        <div class="px-4 py-2 bg-purple-500/10 border border-purple-500/20 rounded-xl text-purple-400 text-sm font-semibold shrink-0">
            Total: {{ $kelasItem->siswa_count }} Siswa
        </div>
    </div>

    <!-- Students Table -->
    <div class="luxury-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[750px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS / NISN</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No. Telepon</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-4 text-white/60 text-sm">
                            {{ $index + 1 }}
                        </td>
                        <td class="p-4">
                            <span class="text-white text-sm font-semibold block">{{ $item->nis }}</span>
                            <span class="text-white/40 text-xs font-mono block mt-0.5">NISN: {{ $item->nisn ?? '-' }}</span>
                        </td>
                        <td class="p-4 text-white font-bold text-sm">
                            {{ $item->nama }}
                        </td>
                        <td class="p-4">
                            @if($item->jenis_kelamin === 'L')
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    Laki-laki
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-pink-500/20 text-pink-300 border border-pink-500/30">
                                    Perempuan
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-white/80 text-sm font-mono">
                            {{ $item->telp ?? '-' }}
                        </td>
                        <td class="p-4 text-white/60 text-xs max-w-xs truncate" title="{{ $item->alamat }}">
                            {{ $item->alamat ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-white/40">
                            <i class="fas fa-users text-4xl mb-3 block"></i>
                            Tidak ada siswa terdaftar di kelas ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

