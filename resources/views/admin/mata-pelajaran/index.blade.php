@extends('layouts.app')

@section('title', 'Data Mata Pelajaran - Pembelajaran Digital')
@section('breadcrumb', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')

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
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Mapel</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $mapel->total() }}</h3>
                    <p class="text-blue-400 text-xs md:text-sm mt-2"><i class="fas fa-book"></i> Aktif</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-book text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Telah Terampu</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\MataPelajaran::whereNotNull('guru_id')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-user-check text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Belum Terampu</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\MataPelajaran::whereNull('guru_id')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-yellow-500/20">
                    <i class="fas fa-user-times text-yellow-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Guru</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ $gurus->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-chalkboard-teacher text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.mata-pelajaran.index') }}" id="searchForm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari kode atau nama mata pelajaran..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select name="sort_by" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="nama_mapel" {{ request('sort_by') == 'nama_mapel' ? 'selected' : '' }}>Nama Mapel</option>
                        <option value="kode_mapel" {{ request('sort_by') == 'kode_mapel' ? 'selected' : '' }}>Kode Mapel</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.mata-pelajaran.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
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

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 flex items-center justify-between">
        <div class="text-sm md:text-base">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400">
        <div class="font-semibold text-sm md:text-base mb-1">
            <i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan input:
        </div>
        <ul class="list-disc list-inside text-xs md:text-sm pl-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table Data Mata Pelajaran -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Daftar Mata Pelajaran</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $mapel->firstItem() ?? 0 }} - {{ $mapel->lastItem() ?? 0 }} dari {{ $mapel->total() }} data</p>
                </div>
                <div>
                    <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Mapel
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[600px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-40">Kode Mapel</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Mata Pelajaran</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Guru Pengampu</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mapel as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $mapel->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->kode_mapel }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $item->nama_mapel }}</td>
                        <td class="p-3 md:p-4 text-sm">
                            @if($item->guru)
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    <span class="text-white/80 font-medium">{{ $item->guru->name }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                                    <span class="text-white/40 italic">Belum ditentukan</span>
                                </div>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-center">
                            <button type="button" 
                                    onclick='openEditModal(@json($item))' 
                                    class="text-blue-400 hover:text-blue-300 mx-1 text-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->nama_mapel) }}')" 
                                    class="text-red-400 hover:text-red-300 mx-1 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-book text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Tidak ada data mata pelajaran</p>
                                <button type="button" onclick="openCreateModal()" class="mt-4 px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm">
                                    <i class="fas fa-plus"></i> Tambah Mapel
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($mapel->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
            <p class="text-white/40 text-sm">
                Showing {{ $mapel->firstItem() }} to {{ $mapel->lastItem() }} of {{ $mapel->total() }} entries
            </p>
            <div class="flex gap-2">
                {{-- Previous Button --}}
                @if($mapel->onFirstPage())
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $mapel->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($mapel->getUrlRange(1, $mapel->lastPage()) as $page => $url)
                    @if($page == $mapel->currentPage())
                        <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if($mapel->hasMorePages())
                    <a href="{{ $mapel->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
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

<!-- Modal Create Mapel - Responsive -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-md mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-plus-circle text-blue-400 mr-2"></i>
                Tambah Mapel
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.mata-pelajaran.store') }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4">
                <div>
                    <label class="text-white/70 text-sm block mb-1">Kode Mapel <span class="text-red-400">*</span></label>
                    <input type="text" name="kode_mapel" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: MP-MAT-10">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Nama Mata Pelajaran <span class="text-red-400">*</span></label>
                    <input type="text" name="nama_mapel" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: Matematika Wajib">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Guru Pengampu</label>
                    <select name="guru_id" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="">-- Pilih Guru Pengampu --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm flex items-center gap-1.5">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Mapel - Responsive -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-md mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-edit text-yellow-400 mr-2"></i>
                Edit Mapel
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4 md:p-6 space-y-4">
                <div>
                    <label class="text-white/70 text-sm block mb-1">Kode Mapel <span class="text-red-400">*</span></label>
                    <input type="text" name="kode_mapel" id="edit_kode_mapel" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Nama Mata Pelajaran <span class="text-red-400">*</span></label>
                    <input type="text" name="nama_mapel" id="edit_nama_mapel" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Guru Pengampu</label>
                    <select name="guru_id" id="edit_guru_id" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="">-- Pilih Guru Pengampu --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
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
    function openEditModal(mapel) {
        document.getElementById('edit_kode_mapel').value = mapel.kode_mapel || '';
        document.getElementById('edit_nama_mapel').value = mapel.nama_mapel || '';
        document.getElementById('edit_guru_id').value = mapel.guru_id || '';
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('admin/mata-pelajaran') }}/" + mapel.id;
        
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
        if (confirm(`Apakah Anda yakin ingin menghapus mata pelajaran "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('admin/mata-pelajaran') }}/" + id;
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
