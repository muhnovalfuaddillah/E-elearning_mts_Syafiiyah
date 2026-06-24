@extends('layouts.app')

@section('title', 'Rekap Jurnal Mengajar - Pembelajaran Digital')
@section('breadcrumb', 'Rekap Jurnal')
@section('page-title', 'Rekap Jurnal Mengajar')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Header Section -->
    <div class="mb-6">
        <h6 class="text-white font-semibold text-lg">Monitoring Jurnal Mengajar Guru</h6>
        <p class="text-white/40 text-xs md:text-sm">Pantau jurnal mengajar harian seluruh guru secara terpusat.</p>
    </div>

    <!-- Filters Section -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.jurnal.index') }}" id="filterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Filter Guru</label>
                    <select name="guru_id" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Guru</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}" class="text-black bg-white" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Filter Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" class="text-black bg-white" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Filter Mata Pelajaran</label>
                    <select name="mapel_id" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Mapel</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" class="text-black bg-white" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Filter Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-purple-500/20 border border-purple-500/30 rounded-lg text-purple-400 font-semibold text-sm hover:bg-purple-500/30 transition-all flex items-center justify-center gap-1.5">
                        <i class="fas fa-filter"></i> Cari
                    </button>
                    @if(request('guru_id') || request('kelas_id') || request('mapel_id') || request('tanggal'))
                        <a href="{{ route('admin.jurnal.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400 flex items-center justify-between">
        <div class="text-sm md:text-base">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Data Table -->
    <div class="luxury-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[900px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-28">Tanggal</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Guru Pengampu</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Kelas</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Pertemuan</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Materi Pokok</th>
                        <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnals as $index => $item)
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="p-4 text-white/80 text-sm">{{ $jurnals->firstItem() + $index }}</td>
                            <td class="p-4 text-white/80 text-sm font-semibold">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="p-4 text-white font-bold text-sm">
                                {{ $item->guru->name ?? 'N/A' }}
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-500/20 text-purple-400">
                                    {{ $item->kelas->kode_kelas }}
                                </span>
                            </td>
                            <td class="p-4 text-white font-medium text-sm">{{ $item->mapel->nama_mapel }}</td>
                            <td class="p-4 text-center text-white/80 text-sm font-bold font-mono">Ke-{{ $item->pertemuan_ke }}</td>
                            <td class="p-4 text-white/80 text-sm max-w-xs truncate" title="{{ $item->materi }}">{{ $item->materi }}</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.jurnal.show', $item->id) }}" class="px-2.5 py-1 bg-purple-500/20 border border-purple-500/30 text-purple-400 text-xs rounded-lg hover:bg-purple-500/30 transition-all">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <form action="{{ route('admin.jurnal.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini sebagai Administrator?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-500/20 border border-red-500/30 text-red-400 text-xs rounded-lg hover:bg-red-500/30 transition-all">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-white/40">
                                <i class="fas fa-file-signature text-5xl mb-3 text-white/10 block"></i>
                                <p>Belum ada jurnal mengajar terdaftar di sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jurnals->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center">
            <p class="text-white/40 text-xs sm:text-sm">
                Showing {{ $jurnals->firstItem() }} to {{ $jurnals->lastItem() }} of {{ $jurnals->total() }} entries
            </p>
            <div>
                {{ $jurnals->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
