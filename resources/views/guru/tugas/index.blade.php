@extends('layouts.app')

@section('title', 'Kelola Tugas - Pembelajaran Digital')
@section('breadcrumb', 'Tugas')
@section('page-title', 'Kelola Tugas')

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
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Tugas</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $tugas->total() }}</h3>
                    <p class="text-blue-400 text-xs md:text-sm mt-2"><i class="fas fa-file-invoice"></i> Dibuat</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-file-alt text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Tugas Aktif</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Tugas::where('guru_id', auth()->id())->where('deadline', '>', now())->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-hourglass-half text-blue-400 text-base md:text-xl animate-pulse"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Tenggat Lewat</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Tugas::where('guru_id', auth()->id())->where('deadline', '<=', now())->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-red-500/20">
                    <i class="fas fa-calendar-times text-red-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Mengajar Kelas</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ $kelas->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-school text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('guru.tugas.index') }}" id="searchForm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari judul tugas atau materi petunjuk..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select name="kelas_id" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" class="text-black bg-white" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-40">
                    <select name="sort_by" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="deadline" {{ request('sort_by') == 'deadline' ? 'selected' : '' }}>Tenggat Terdekat</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru Dibuat</option>
                        <option value="judul" {{ request('sort_by') == 'judul' ? 'selected' : '' }}>Judul Tugas</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('guru.tugas.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-400 flex items-center justify-between">
        <div class="text-sm md:text-base">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-emerald-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400">
        <div class="font-semibold text-sm md:text-base mb-1">
            <i class="fas fa-times-circle mr-2"></i> Gagal menyimpan data tugas:
        </div>
        <ul class="list-disc list-inside text-xs md:text-sm pl-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table Data Tugas -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Daftar Tugas Mandiri</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $tugas->firstItem() ?? 0 }} - {{ $tugas->lastItem() ?? 0 }} dari {{ $tugas->total() }} data</p>
                </div>
                <div>
                    <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm flex items-center gap-2">
                        <i class="fas fa-plus"></i> Publikasikan Tugas Baru
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Judul Tugas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Tenggat Waktu</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Lampiran</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tugas as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $tugas->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4">
                            <div class="text-white font-medium text-sm">{{ $item->judul }}</div>
                            <div class="text-white/40 text-xs mt-0.5 truncate max-w-[200px]" title="{{ strip_tags($item->deskripsi) }}">
                                {{ strip_tags($item->deskripsi) }}
                            </div>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->mapel->nama_mapel ?? '-' }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->kelas->kode_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            <div class="font-medium text-xs md:text-sm">{{ $item->deadline->format('d M Y') }}</div>
                            <div class="text-white/40 text-xs">{{ $item->deadline->format('H:i') }} WIB</div>
                        </td>
                        <td class="p-3 md:p-4 text-sm">
                            @if($item->deadline->isFuture())
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">Aktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-red-500/20 text-red-400">Selesai</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-sm">
                            @if($item->file_tugas)
                                <a href="{{ asset('storage/' . $item->file_tugas) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-400 hover:text-blue-300 font-medium">
                                    <i class="fas fa-paperclip"></i> Unduh File
                                </a>
                            @else
                                <span class="text-white/30 italic text-xs">Tidak ada lampiran</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('guru.tugas.show', $item->id) }}" 
                                   class="px-2 py-1 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded border border-blue-500/20 text-xs flex items-center gap-1 transition"
                                   title="Lihat Pengumpulan Siswa">
                                    <i class="fas fa-users"></i>
                                    <span class="hidden md:inline">Siswa</span>
                                </a>
                                <button type="button" 
                                        onclick='openEditModal(@json($item))' 
                                        class="px-2 py-1 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded border border-blue-500/20 text-xs transition"
                                        title="Edit Tugas">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                        onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->judul) }}')" 
                                        class="px-2 py-1 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded border border-red-500/20 text-xs transition"
                                        title="Hapus Tugas">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-file-alt text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Belum ada tugas yang Anda buat</p>
                                <button type="button" onclick="openCreateModal()" class="mt-4 px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm">
                                    <i class="fas fa-plus"></i> Buat Tugas Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($tugas->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
            <p class="text-white/40 text-sm">
                Showing {{ $tugas->firstItem() }} to {{ $tugas->lastItem() }} of {{ $tugas->total() }} entries
            </p>
            <div class="flex gap-2">
                {{-- Previous Button --}}
                @if($tugas->onFirstPage())
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $tugas->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($tugas->getUrlRange(1, $tugas->lastPage()) as $page => $url)
                    @if($page == $tugas->currentPage())
                        <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if($tugas->hasMorePages())
                    <a href="{{ $tugas->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
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

<!-- Modal Create Tugas - Responsive -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold flex items-center">
                <i class="fas fa-plus-circle text-blue-400 mr-2"></i>
                Publikasikan Tugas Baru
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('guru.tugas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-4 md:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="text-white/70 text-sm block mb-1">Judul Tugas <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: Latihan Logika Pemrograman Dasar">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Pilih Kelas <span class="text-red-400">*</span></label>
                        <select name="kelas_id" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Mata Pelajaran <span class="text-red-400">*</span></label>
                        <select name="mapel_id" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Tenggat Waktu (Deadline) <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="deadline" required
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">File Lampiran <span class="text-xs text-white/40">(PDF, Word, ZIP, Gambar - Max 5MB)</span></label>
                        <input type="file" name="file_tugas"
                               class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white/70 focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                </div>

                <div>
                    <label class="text-white/70 text-sm block mb-1">Petunjuk Tugas / Deskripsi <span class="text-red-400">*</span></label>
                    <textarea name="deskripsi" rows="5" required
                              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                              placeholder="Tuliskan petunjuk tugas atau pertanyaan di sini..."></textarea>
                </div>
            </div>
            
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm flex items-center gap-1.5">
                    <i class="fas fa-paper-plane"></i> Publikasikan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Tugas - Responsive -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-edit text-yellow-400 mr-2"></i>
                Edit Tugas
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="p-4 md:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="text-white/70 text-sm block mb-1">Judul Tugas <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" id="edit_judul" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Pilih Kelas <span class="text-red-400">*</span></label>
                        <select name="kelas_id" id="edit_kelas_id" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Mata Pelajaran <span class="text-red-400">*</span></label>
                        <select name="mapel_id" id="edit_mapel_id" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Tenggat Waktu (Deadline) <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="deadline" id="edit_deadline" required
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Ganti File Lampiran <span class="text-xs text-white/40">(Biarkan kosong jika tetap)</span></label>
                        <input type="file" name="file_tugas"
                               class="w-full px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white/70 focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                </div>

                <div>
                    <label class="text-white/70 text-sm block mb-1">Petunjuk Tugas / Deskripsi <span class="text-red-400">*</span></label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="5" required
                              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"></textarea>
                </div>
            </div>
            
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg text-white font-semibold text-sm flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Form Delete -->
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

    // Auto submit search dengan debounce
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500);
        });
    }

    // Modal Create
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Modal Edit
    function openEditModal(tugas) {
        document.getElementById('edit_judul').value = tugas.judul || '';
        document.getElementById('edit_kelas_id').value = tugas.kelas_id || '';
        document.getElementById('edit_mapel_id').value = tugas.mapel_id || '';
        document.getElementById('edit_deskripsi').value = tugas.deskripsi || '';
        
        // Format ISO-8601 ke HTML5 datetime-local format (YYYY-MM-DDTHH:MM)
        if (tugas.deadline) {
            let dateObj = new Date(tugas.deadline);
            let offset = dateObj.getTimezoneOffset() * 60000; // milidetik
            let localISOTime = (new Date(dateObj.getTime() - offset)).toISOString().slice(0, 16);
            document.getElementById('edit_deadline').value = localISOTime;
        } else {
            document.getElementById('edit_deadline').value = '';
        }
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('guru/tugas') }}/" + tugas.id;
        
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Confirm Delete
    function confirmDelete(id, nama) {
        if (confirm(`Apakah Anda yakin ingin menghapus data tugas "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('guru/tugas') }}/" + id;
            form.submit();
        }
    }

    // Close modals when clicking outside
    const createModal = document.getElementById('createModal');
    const editModal = document.getElementById('editModal');
    
    if (createModal) {
        createModal.addEventListener('click', function(e) {
            if (e.target === this) closeCreateModal();
        });
    }
    
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
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
