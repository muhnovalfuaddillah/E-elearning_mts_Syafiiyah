@extends('layouts.app')

@section('title', 'Materi Pelajaran Saya - Pembelajaran Digital')
@section('breadcrumb', 'Materi Pelajaran')
@section('page-title', 'Materi Pelajaran Saya')

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
                    <p class="text-white/50 text-xs uppercase tracking-wider">Materi Saya</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $materi->total() }}</h3>
                    <p class="text-blue-400 text-xs md:text-sm mt-2"><i class="fas fa-book"></i> Aktif</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-folder text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">File PDF</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Materi::where('user_id', auth()->id())->where('tipe', 'pdf')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-red-500/20">
                    <i class="fas fa-file-pdf text-red-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">File PPT</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Materi::where('user_id', auth()->id())->where('tipe', 'ppt')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-orange-500/20">
                    <i class="fas fa-file-powerpoint text-orange-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Media Video</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Materi::where('user_id', auth()->id())->where('tipe', 'video')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-video text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('guru.materi.index') }}" id="searchForm">
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari judul materi saya..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
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
                            <option value="" class="text-black bg-white">Semua Mapel</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}" class="text-black bg-white" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <select name="sort_by" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="judul" {{ request('sort_by') == 'judul' ? 'selected' : '' }}>Judul Materi</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('guru.materi.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
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

    <!-- Table Data Materi -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Materi Pembelajaran Saya</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $materi->firstItem() ?? 0 }} - {{ $materi->lastItem() ?? 0 }} dari {{ $materi->total() }} data</p>
                </div>
                <div>
                    <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm flex items-center gap-2">
                        <i class="fas fa-plus"></i> Terbitkan Materi Baru
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Judul Materi</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Tanggal Rilis</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Format</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Media / File</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materi as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $materi->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4">
                            <div class="text-white font-medium text-sm">{{ $item->judul }}</div>
                            <div class="text-white/40 text-xs mt-0.5 truncate max-w-[200px]" title="{{ $item->deskripsi }}">
                                {{ $item->deskripsi ?? 'Tidak ada deskripsi' }}
                            </div>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->mapel->nama_mapel ?? '-' }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->kelas->kode_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            {{ $item->created_at->format('d M Y H:i') }} WIB
                        </td>
                        <td class="p-3 md:p-4 text-sm">
                            @if($item->tipe == 'pdf')
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-red-500/20 text-red-400"><i class="fas fa-file-pdf"></i> PDF</span>
                            @elseif($item->tipe == 'ppt')
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-orange-500/20 text-orange-400"><i class="fas fa-file-powerpoint"></i> PowerPoint</span>
                            @elseif($item->tipe == 'video')
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400"><i class="fas fa-video"></i> Video Tautan</span>
                            @else
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-gray-500/20 text-gray-400"><i class="fas fa-file-alt"></i> Lainnya</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-sm">
                            @if($item->file_materi)
                                <a href="{{ asset('storage/' . $item->file_materi) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-400 hover:text-emerald-300 font-semibold">
                                    <i class="fas fa-download"></i> Unduh File
                                </a>
                            @elseif($item->link_video)
                                <a href="{{ $item->link_video }}" target="_blank" class="inline-flex items-center gap-1 text-blue-400 hover:text-blue-300 font-semibold">
                                    <i class="fas fa-external-link-alt"></i> Buka Video
                                </a>
                            @else
                                <span class="text-white/30 italic text-xs">Materi Teks</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-center">
                            <button type="button" 
                                    onclick='openEditModal(@json($item))' 
                                    class="text-blue-400 hover:text-blue-300 mx-1 text-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->judul) }}')" 
                                    class="text-red-400 hover:text-red-300 mx-1 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-book text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Belum ada materi pelajaran yang Anda terbitkan</p>
                                <button type="button" onclick="openCreateModal()" class="mt-4 px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm">
                                    <i class="fas fa-plus"></i> Terbitkan Materi
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($materi->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
            <p class="text-white/40 text-sm">
                Showing {{ $materi->firstItem() }} to {{ $materi->lastItem() }} of {{ $materi->total() }} entries
            </p>
            <div class="flex gap-2">
                @if($materi->onFirstPage())
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $materi->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                @foreach ($materi->getUrlRange(1, $materi->lastPage()) as $page => $url)
                    @if($page == $materi->currentPage())
                        <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">{{ $page }}</a>
                    @endif
                @endforeach

                @if($materi->hasMorePages())
                    <a href="{{ $materi->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
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

<!-- Modal Create Materi - Responsive -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold flex items-center">
                <i class="fas fa-plus-circle text-blue-400 mr-2"></i>
                Terbitkan Materi Baru
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('guru.materi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-4 md:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label class="text-white/70 text-sm block mb-1">Judul Materi <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: Pengenalan Aljabar Linear">
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

                <div class="p-4 bg-white/5 rounded-xl border border-white/10 space-y-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider"><i class="fas fa-file-upload"></i> Lampirkan File atau Video (Opsional)</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs md:text-sm">
                        <div>
                            <label class="text-white/70 block mb-1">File Dokumen <span class="text-white/30">(PDF, PPT, Word, Excel - Max 10MB)</span></label>
                            <input type="file" name="file_materi"
                                   class="w-full px-2 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white/75 text-xs focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-white/70 block mb-1">Tautan Video <span class="text-white/30">(YouTube / Google Drive Link)</span></label>
                            <input type="url" name="link_video"
                                   class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-xs"
                                   placeholder="https://youtube.com/...">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-white/70 text-sm block mb-1">Keterangan Tambahan / Deskripsi</label>
                    <textarea name="deskripsi" rows="4"
                              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                              placeholder="Ketik keterangan tambahan mengenai materi di sini..."></textarea>
                </div>
            </div>
            
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Terbitkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Materi - Responsive -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-edit text-yellow-400 mr-2"></i>
                Edit Materi Pelajaran
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
                    <label class="text-white/70 text-sm block mb-1">Judul Materi <span class="text-red-400">*</span></label>
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

                <div class="p-4 bg-white/5 rounded-xl border border-white/10 space-y-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider"><i class="fas fa-file-upload"></i> Ganti Lampiran File / Tautan Video</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs md:text-sm">
                        <div>
                            <label class="text-white/70 block mb-1">Ganti File Dokumen <span class="text-xs text-white/40">(Kosongkan jika tetap)</span></label>
                            <input type="file" name="file_materi"
                                   class="w-full px-2 py-1.5 bg-white/5 border border-white/10 rounded-lg text-white/75 text-xs focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-white/70 block mb-1">Ubah Tautan Video</label>
                            <input type="url" name="link_video" id="edit_link_video"
                                   class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-xs">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-white/70 text-sm block mb-1">Keterangan Tambahan / Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="4"
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
    function openEditModal(materi) {
        document.getElementById('edit_judul').value = materi.judul || '';
        document.getElementById('edit_kelas_id').value = materi.kelas_id || '';
        document.getElementById('edit_mapel_id').value = materi.mapel_id || '';
        document.getElementById('edit_link_video').value = materi.link_video || '';
        document.getElementById('edit_deskripsi').value = materi.deskripsi || '';
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('guru/materi') }}/" + materi.id;
        
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
        if (confirm(`Apakah Anda yakin ingin menghapus materi pelajaran "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('guru/materi') }}/" + id;
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
