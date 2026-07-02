@extends('layouts.app')

@section('title', 'Kelola Ujian - Pembelajaran Digital')
@section('breadcrumb', 'Ujian')
@section('page-title', 'Kelola Ujian')

<style>
    select option {
        color: black !important;
        background-color: white !important;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Ujian</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $ujians->total() }}</h3>
                    <p class="text-blue-400 text-xs md:text-sm mt-2"><i class="fas fa-file-signature"></i> Terdata</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-file-signature text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Status Aktif</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-emerald-400">
                        {{ \App\Models\Ujian::where('guru_id', auth()->id())->where('status', 'published')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-emerald-500/20">
                    <i class="fas fa-play text-emerald-400 text-base md:text-xl animate-pulse"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Draft / Draf</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-yellow-400">
                        {{ \App\Models\Ujian::where('guru_id', auth()->id())->where('status', 'draft')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-yellow-500/20">
                    <i class="fas fa-edit text-yellow-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Ditutup</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-red-400">
                        {{ \App\Models\Ujian::where('guru_id', auth()->id())->where('status', 'closed')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-red-500/20">
                    <i class="fas fa-lock text-red-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('guru.ujian.index') }}" id="searchForm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               placeholder="Cari judul ujian..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select name="kelas_id" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('guru.ujian.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

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

    <!-- Table Data Ujian -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Daftar Ujian Online</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $ujians->firstItem() ?? 0 }} - {{ $ujians->lastItem() ?? 0 }} dari {{ $ujians->total() }} data</p>
                </div>
                <div>
                    <a href="{{ route('guru.ujian.create') }}" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm flex items-center gap-2 font-semibold">
                        <i class="fas fa-plus"></i> Buat Ujian Baru
                    </a>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Judul Ujian</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Jadwal Ujian</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Durasi</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ujians as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $ujians->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4">
                            <div class="text-white font-medium text-sm">{{ $item->judul }}</div>
                            <div class="text-white/40 text-xs mt-0.5">{{ $item->soals_count ?? $item->soals()->count() }} Soal Pilihan Ganda</div>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->mapel->nama_mapel ?? '-' }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->kelas->kode_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            <div class="text-xs">Mulai: {{ $item->waktu_mulai->format('d M Y, H:i') }} WIB</div>
                            <div class="text-xs text-white/40">Selesai: {{ $item->waktu_selesai->format('d M Y, H:i') }} WIB</div>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            <span class="inline-flex items-center gap-1.5"><i class="far fa-clock text-blue-400"></i> {{ $item->durasi }} Menit</span>
                        </td>
                        <td class="p-3 md:p-4">
                            @if($item->status == 'published')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Aktif</span>
                            @elseif($item->status == 'draft')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Draf</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">Ditutup</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('guru.ujian.soal', $item->id) }}" class="p-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-400 rounded-lg text-xs font-semibold flex items-center gap-1" title="Kelola Soal">
                                    <i class="fas fa-list-ol"></i> Soal
                                </a>
                                <a href="{{ route('guru.ujian.hasil', $item->id) }}" class="p-2 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 rounded-lg text-xs font-semibold flex items-center gap-1" title="Hasil Nilai">
                                    <i class="fas fa-poll"></i> Hasil
                                </a>
                                <a href="{{ route('guru.ujian.edit', $item->id) }}" class="p-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 rounded-lg text-xs" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('guru.ujian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini? Semua data terkait soal dan hasil nilai siswa akan terhapus permanen.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-xs" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-white/40 text-sm">
                            <i class="fas fa-file-signature text-5xl text-white/10 mb-3 block"></i>
                            Belum ada data ujian yang dibuat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ujians->hasPages())
        <div class="p-4 border-t border-white/10 bg-white/5">
            {{ $ujians->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
