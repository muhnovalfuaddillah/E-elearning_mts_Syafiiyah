@extends('layouts.app')

@section('title', 'Data Kelas - MTs Syafiiyah')
@section('breadcrumb', 'Kelas')
@section('page-title', 'Data Kelas')
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

<
@section('content')
<div class="w-full px-4 md:px-6 py-6">
    

    <!-- Stats Row - Responsive -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Kelas</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $kelas->total() }}</h3>
                    <p class="text-blue-400 text-xs md:text-sm mt-2"><i class="fas fa-school"></i> Active</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-school text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Kelas VII</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $kelas->where('tingkat', '7')->count() + $kelas->where('tingkat', 'VII')->count() }}</h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-layer-group text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Kelas VIII</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $kelas->where('tingkat', '8')->count() + $kelas->where('tingkat', 'VIII')->count() }}</h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-layer-group text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Kelas IX</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $kelas->where('tingkat', '9')->count() + $kelas->where('tingkat', 'IX')->count() }}</h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-layer-group text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.kelas.index') }}" id="searchForm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari kode kelas, nama kelas, atau jurusan..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm md:text-base">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="w-full md:w-40">
                    <select name="tingkat" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm md:text-base">
                        <option value="" class="text-black bg-white">Semua Tingkat</option>
                        <option value="7" class="text-black bg-white" {{ request('tingkat') == '7' || request('tingkat') == 'VII' ? 'selected' : '' }}>Kelas VII</option>
                        <option value="8" class="text-black bg-white" {{ request('tingkat') == '8' || request('tingkat') == 'VIII' ? 'selected' : '' }}>Kelas VIII</option>
                        <option value="9" class="text-black bg-white" {{ request('tingkat') == '9' || request('tingkat') == 'IX' ? 'selected' : '' }}>Kelas IX</option>
                    </select>
                </div>
                <div class="w-full md:w-40">
                    <select name="sort_by" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm md:text-base">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="kode_kelas" {{ request('sort_by') == 'kode_kelas' ? 'selected' : '' }}>Kode Kelas</option>
                        <option value="nama_kelas" {{ request('sort_by') == 'nama_kelas' ? 'selected' : '' }}>Nama Kelas</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm md:text-base">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.kelas.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm md:text-base">
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

    <!-- Table Data Kelas -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Daftar Kelas</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $kelas->firstItem() }} - {{ $kelas->lastItem() }} dari {{ $kelas->total() }} data</p>
                </div>
                <div>
                    <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm md:text-base">
                        <i class="fas fa-plus"></i> Tambah Data Kelas
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[600px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Kode Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Tingkat</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Jurusan</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $kelas->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->kode_kelas }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $item->nama_kelas }}</td>
                        <td class="p-3 md:p-4">
                            @php
                                $tingkatRomawi = ['7' => 'VII', '8' => 'VIII', '9' => 'IX', 'VII' => 'VII', 'VIII' => 'VIII', 'IX' => 'IX'];
                                $romawi = $tingkatRomawi[$item->tingkat] ?? $item->tingkat;
                            @endphp
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                Kelas {{ $romawi }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->jurusan }}</td>
                        <td class="p-3 md:p-4 text-center">
                            <button type="button" 
                                    onclick='openEditModal(@json($item))' 
                                    class="text-blue-400 hover:text-blue-300 mx-1 text-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->nama_kelas) }}')" 
                                    class="text-red-400 hover:text-red-300 mx-1 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-school text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Tidak ada data kelas</p>
                                <button type="button" onclick="openCreateModal()" class="mt-4 px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400">
                                    <i class="fas fa-plus"></i> Tambah Kelas
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
@if($kelas->hasPages())
<div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
    <p class="text-white/40 text-sm">
        Showing {{ $kelas->firstItem() }} to {{ $kelas->lastItem() }} of {{ $kelas->total() }} entries
    </p>
    <div class="flex gap-2">
        {{-- Previous Button --}}
        @if($kelas->onFirstPage())
            <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
        @else
            <a href="{{ $kelas->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
                <i class="fas fa-chevron-left"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($kelas->getUrlRange(1, $kelas->lastPage()) as $page => $url)
            @if($page == $kelas->currentPage())
                <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm">{{ $page }}</a>
            @else
                <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if($kelas->hasMorePages())
            <a href="{{ $kelas->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
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

</div>

<!-- Modal Create Kelas - Responsive -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-md mx-4 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-plus-circle text-blue-400 mr-2"></i>
                Tambah Kelas
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.kelas.store') }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4">
                <div>
                    <label class="text-white/70 text-sm block mb-2">Kode Kelas <span class="text-red-400">*</span></label>
                    <input type="text" name="kode_kelas" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: KLS-7A">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-2">Nama Kelas <span class="text-red-400">*</span></label>
                    <input type="text" name="nama_kelas" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: 7-A">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-2">Tingkat <span class="text-red-400">*</span></label>
                    <select name="tingkat" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="">Pilih Tingkat</option>
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                    </select>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-2">Jurusan <span class="text-red-400">*</span></label>
                    <input type="text" name="jurusan" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: Rekayasa Perangkat Lunak">
                </div>
            </div>
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kelas - Responsive -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-md mx-4 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-edit text-yellow-400 mr-2"></i>
                Edit Kelas
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
                    <label class="text-white/70 text-sm block mb-2">Kode Kelas <span class="text-red-400">*</span></label>
                    <input type="text" name="kode_kelas" id="edit_kode_kelas" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-2">Nama Kelas <span class="text-red-400">*</span></label>
                    <input type="text" name="nama_kelas" id="edit_nama_kelas" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-2">Tingkat <span class="text-red-400">*</span></label>
                    <select name="tingkat" id="edit_tingkat" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                    </select>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-2">Jurusan <span class="text-red-400">*</span></label>
                    <input type="text" name="jurusan" id="edit_jurusan" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
            </div>
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg text-white font-semibold text-sm">
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
    function openEditModal(kelas) {
        document.getElementById('edit_kode_kelas').value = kelas.kode_kelas || '';
        document.getElementById('edit_nama_kelas').value = kelas.nama_kelas || '';
        document.getElementById('edit_tingkat').value = kelas.tingkat || '';
        document.getElementById('edit_jurusan').value = kelas.jurusan || '';
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('admin/kelas') }}/" + kelas.id;
        
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
        if (confirm(`Apakah Anda yakin ingin menghapus kelas "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('admin/kelas') }}/" + id;
            form.submit();
        }
    }

    // Close modal when clicking outside
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

    /* Responsive table */
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
