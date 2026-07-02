@extends('layouts.app')

@section('title', 'Rekap Absensi Siswa - Pembelajaran Digital')
@section('breadcrumb', 'Absensi')
@section('page-title', 'Rekap Absensi Siswa')

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
    @php
        $total = \App\Models\Absensi::count();
        $hadir = \App\Models\Absensi::where('status', 'H')->count();
        $sakitIzin = \App\Models\Absensi::whereIn('status', ['S', 'I'])->count();
        $alpa = \App\Models\Absensi::where('status', 'A')->count();
        $persentase = $total > 0 ? ($hadir / $total) * 100 : 100;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Absensi</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $total }}</h3>
                    <p class="text-blue-400 text-xs md:text-sm mt-2"><i class="fas fa-list-ol"></i> Records</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-calendar-check text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Tingkat Kehadiran</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ number_format($persentase, 1) }}%
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-percent text-blue-400 text-base md:text-xl animate-pulse"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Sakit & Izin (S/I)</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ $sakitIzin }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-yellow-500/20">
                    <i class="fas fa-user-clock text-yellow-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Alpa / Bolos (A)</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ $alpa }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-red-500/20">
                    <i class="fas fa-user-times text-red-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.absensi.index') }}" id="searchForm">
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari nama atau NIS siswa..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <select name="kelas_id" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="" class="text-black bg-white">Semua Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" class="text-black bg-white" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="mapel_id" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="" class="text-black bg-white">Semua Pelajaran</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" class="text-black bg-white" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="" class="text-black bg-white">Semua Status</option>
                            <option value="H" class="text-black bg-white" {{ request('status') == 'H' ? 'selected' : '' }}>Hadir (H)</option>
                            <option value="S" class="text-black bg-white" {{ request('status') == 'S' ? 'selected' : '' }}>Sakit (S)</option>
                            <option value="I" class="text-black bg-white" {{ request('status') == 'I' ? 'selected' : '' }}>Izin (I)</option>
                            <option value="A" class="text-black bg-white" {{ request('status') == 'A' ? 'selected' : '' }}>Alpa (A)</option>
                        </select>
                    </div>
                    <div>
                        <input type="date" name="start_date" 
                               value="{{ request('start_date') }}"
                               onchange="document.getElementById('searchForm').submit()"
                               class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                               title="Tanggal Mulai">
                    </div>
                    <div>
                        <input type="date" name="end_date" 
                               value="{{ request('end_date') }}"
                               onchange="document.getElementById('searchForm').submit()"
                               class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                               title="Tanggal Akhir">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.absensi.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Data Rekap Absensi -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Rekapitulasi Kehadiran Siswa</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $absensi->firstItem() ?? 0 }} - {{ $absensi->lastItem() ?? 0 }} dari {{ $absensi->total() }} data</p>
                </div>
                <div>
                    <a href="{{ route('admin.absensi.export', request()->query()) }}" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm flex items-center gap-1.5 font-semibold">
                        <i class="fas fa-file-csv"></i> Ekspor Absensi (CSV/Excel)
                    </a>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Tanggal</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-28">Jam Absen</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider font-medium">Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider font-medium">Mata Pelajaran</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider font-medium">Jam Mengajar</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Status</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Guru Pencatat</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Keterangan</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $absensi->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4 text-white font-medium text-sm">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            {{ $item->created_at ? $item->created_at->format('H:i') : '-' }} WIB
                        </td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->siswa->nis ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $item->siswa->nama ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->kelas->kode_kelas ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->mapel->nama_mapel ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            @if($item->jadwalPelajaran)
                                {{ $item->jadwalPelajaran->hari }} ({{ $item->jadwalPelajaran->jam_mulai }} - {{ $item->jadwalPelajaran->jam_selesai }})
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-center text-sm">
                            @if($item->status == 'H')
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">Hadir</span>
                            @elseif($item->status == 'S')
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">Sakit</span>
                            @elseif($item->status == 'I')
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-yellow-500/20 text-yellow-400">Izin</span>
                            @elseif($item->status == 'A')
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-red-500/20 text-red-400">Alpa</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            {{ $item->guru ? $item->guru->name : 'Sistem (QR Code)' }}
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->keterangan ?? '-' }}</td>
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
                        <td colspan="12" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-calendar-times text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Belum ada rekap absensi masuk untuk filter ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($absensi->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
            <p class="text-white/40 text-sm">
                Showing {{ $absensi->firstItem() }} to {{ $absensi->lastItem() }} of {{ $absensi->total() }} entries
            </p>
            <div class="flex gap-2">
                @if($absensi->onFirstPage())
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $absensi->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                @foreach ($absensi->getUrlRange(1, $absensi->lastPage()) as $page => $url)
                    @if($page == $absensi->currentPage())
                        <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">{{ $page }}</a>
                    @endif
                @endforeach

                @if($absensi->hasMorePages())
                    <a href="{{ $absensi->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
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
        if (confirm(`Apakah Anda yakin ingin menghapus pencatatan absensi siswa "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('admin/absensi') }}/" + id;
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
