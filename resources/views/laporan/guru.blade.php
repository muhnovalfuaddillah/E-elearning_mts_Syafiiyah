@extends('layouts.app')

@section('title', 'Laporan Guru - MTs Syafiiyah')
@section('breadcrumb', 'Laporan Guru')
@section('page-title', 'Laporan Guru')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Action Card -->
    <div class="luxury-card p-5 mb-6 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h5 class="text-white font-bold text-lg">Laporan Data Guru Aktif</h5>
            <p class="text-white/40 text-sm">Unduh atau cetak data profil guru terdaftar di sekolah.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.laporan.guru', ['export' => 'pdf']) }}" target="_blank" class="w-full sm:w-auto px-5 py-2.5 bg-pink-500/20 border border-pink-500/30 rounded-xl text-pink-400 font-semibold text-sm hover:bg-pink-500/35 transition-all text-center">
                <i class="fas fa-file-pdf mr-1.5"></i> Cetak PDF
            </a>
            <a href="{{ route('admin.laporan.guru', ['export' => 'excel']) }}" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-400 font-semibold text-sm hover:bg-emerald-500/35 transition-all text-center">
                <i class="fas fa-file-excel mr-1.5"></i> Ekspor Excel
            </a>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="luxury-card overflow-hidden">
        <div class="p-6 border-b border-white/10 flex justify-between items-center">
            <div>
                <h6 class="text-white font-semibold text-lg">Pratinjau Data Guru</h6>
                <p class="text-white/40 text-sm">Menampilkan seluruh data guru aktif</p>
            </div>
            <span class="px-3 py-1 rounded-xl text-xs font-semibold bg-purple-500/20 text-purple-400 border border-purple-500/30">
                {{ $gurus->count() }} Guru
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[750px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIP</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Guru</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Email</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Gender</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mapel Diampu</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No. Telp</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-4 text-white/60 text-sm">{{ $index + 1 }}</td>
                        <td class="p-4 text-white text-sm font-semibold font-mono">{{ $item->nip ?? '-' }}</td>
                        <td class="p-4 text-white font-bold text-sm">{{ $item->name }}</td>
                        <td class="p-4 text-white/80 text-sm">{{ $item->email }}</td>
                        <td class="p-4">
                            @if($item->jenis_kelamin === 'L')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400">Laki-laki</span>
                            @elseif($item->jenis_kelamin === 'P')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-pink-500/10 text-pink-400">Perempuan</span>
                            @else
                                <span class="text-white/40 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-white/80 text-sm">{{ $item->mapel ?? '-' }}</td>
                        <td class="p-4 text-white/80 text-sm font-mono">{{ $item->telp ?? '-' }}</td>
                        <td class="p-4 text-white/60 text-xs truncate max-w-xs" title="{{ $item->alamat }}">
                            {{ $item->alamat ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-white/40">
                            <i class="fas fa-chalkboard-teacher text-4xl mb-3 block"></i>
                            Tidak ada data guru terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

