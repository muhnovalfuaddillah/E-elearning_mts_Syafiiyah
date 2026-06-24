@extends('layouts.app')

@section('title', 'Rekap Penilaian Siswa - Pembelajaran Digital')
@section('breadcrumb', 'Penilaian')
@section('page-title', 'Rekap Penilaian Siswa')

<style>
    /* Membuat option menjadi hitam */
    select option {
        color: black !important;
        background-color: white !important;
    }
    
    /* Untuk select yang terbuka di mobile */
    select optgroup {
        color: black !important;
        background-color: white !important;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Stats Row - Responsive -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Data Nilai</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $penilaian->total() }}</h3>
                    <p class="text-emerald-400 text-xs md:text-sm mt-2"><i class="fas fa-list-ol"></i> Records</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-graduation-cap text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Rata-rata Nilai Akhir</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ number_format(\App\Models\Penilaian::avg('nilai_akhir') ?? 0, 1) }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-chart-line text-blue-400 text-base md:text-xl animate-pulse"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Nilai Akhir Tertinggi</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ number_format(\App\Models\Penilaian::max('nilai_akhir') ?? 0, 1) }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-emerald-500/20">
                    <i class="fas fa-award text-emerald-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Nilai Akhir Terendah</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ number_format(\App\Models\Penilaian::min('nilai_akhir') ?? 0, 1) }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-red-500/20">
                    <i class="fas fa-arrow-down text-red-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.penilaian.index') }}" id="searchForm">
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari berdasarkan nama atau NIS siswa..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div>
                        <select name="kelas_id" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                            <option value="" class="text-black bg-white">Semua Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" class="text-black bg-white" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="mapel_id" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                            <option value="" class="text-black bg-white">Semua Mapel</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" class="text-black bg-white" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <select name="sort_by" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="nilai_akhir" {{ request('sort_by') == 'nilai_akhir' ? 'selected' : '' }}>Nilai Akhir</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.penilaian.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
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

    <!-- Table Data Rekap Nilai -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Rekap Penilaian Akademik</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $penilaian->firstItem() ?? 0 }} - {{ $penilaian->lastItem() ?? 0 }} dari {{ $penilaian->total() }} data</p>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Rata Harian (40%)</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">UTS (30%)</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">UAS (30%)</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nilai Akhir</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penilaian as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $penilaian->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-500/20 text-purple-400">
                                {{ $item->siswa->nis ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $item->siswa->nama ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->siswa->kelas->kode_kelas ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->mapel->nama_mapel ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-center text-white font-semibold text-sm">{{ $item->nilai_harian !== null ? number_format($item->nilai_harian, 1) : '-' }}</td>
                        <td class="p-3 md:p-4 text-center text-white font-semibold text-sm">{{ $item->nilai_uts ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-center text-white font-semibold text-sm">{{ $item->nilai_uas ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-center">
                            <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $item->nilai_akhir >= 75 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ number_format($item->nilai_akhir, 1) }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-center">
                            <button type="button" 
                                    onclick="confirmReset({{ $item->id }}, '{{ addslashes($item->siswa->nama ?? '') }}')" 
                                    class="text-red-400 hover:text-red-300 mx-1 text-sm flex items-center justify-center gap-1 w-full hover:underline">
                                <i class="fas fa-redo-alt"></i> Reset
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-star text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Belum ada rekap penilaian masuk untuk filter ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($penilaian->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
            <p class="text-white/40 text-sm">
                Showing {{ $penilaian->firstItem() }} to {{ $penilaian->lastItem() }} of {{ $penilaian->total() }} entries
            </p>
            <div class="flex gap-2">
                @if($penilaian->onFirstPage())
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $penilaian->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-purple-500/20 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                @foreach ($penilaian->getUrlRange(1, $penilaian->lastPage()) as $page => $url)
                    @if($page == $penilaian->currentPage())
                        <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-purple-500/20 transition">{{ $page }}</a>
                    @endif
                @endforeach

                @if($penilaian->hasMorePages())
                    <a href="{{ $penilaian->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-purple-500/20 transition">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
        </div>
        @endif      
    </div>
</div>

<!-- Form Reset / Delete -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Clear search
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('searchForm').submit();
    }

    // Confirm Reset
    function confirmReset(id, nama) {
        if (confirm(`Apakah Anda yakin ingin mereset / menghapus nilai untuk siswa "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('admin/penilaian') }}/" + id;
            form.submit();
        }
    }
</script>

<style>
    /* Custom Pagination Styling */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .pagination .page-item {
        list-style: none;
    }
    
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 0.5rem;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s;
        font-size: 0.875rem;
    }
    
    .pagination .page-link:hover {
        background: rgba(168, 85, 247, 0.2);
        color: #a855f7;
    }
    
    .pagination .active .page-link {
        background: linear-gradient(135deg, #a855f7, #ec4899);
        color: white;
    }
    
    .pagination .disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .luxury-card {
            border-radius: 1rem;
        }
        
        .pagination .page-link {
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
        }
    }
</style>

@endsection
