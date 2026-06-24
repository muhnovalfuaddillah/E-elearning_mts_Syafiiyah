@extends('layouts.app')

@section('title', 'Data Guru - Pembelajaran Digital')
@section('breadcrumb', 'Guru')
@section('page-title', 'Data Guru')

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
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Guru</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $gurus->total() }}</h3>
                    <p class="text-emerald-400 text-xs md:text-sm mt-2"><i class="fas fa-user-tie"></i> Aktif</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-chalkboard-teacher text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Laki-laki (L)</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\User::where('role', 'guru')->where('jenis_kelamin', 'L')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-blue-500/20">
                    <i class="fas fa-mars text-blue-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Perempuan (P)</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\User::where('role', 'guru')->where('jenis_kelamin', 'P')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-pink-500/20">
                    <i class="fas fa-venus text-pink-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Mata Pelajaran</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\User::where('role', 'guru')->whereNotNull('mapel')->groupBy('mapel')->pluck('mapel')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-purple-500/20">
                    <i class="fas fa-book-open text-purple-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.guru.index') }}" id="searchForm">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari nama, email, NIP, atau mata pelajaran..." 
                               value="{{ request('search') }}"
                               class="w-full pl-10 pr-10 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm md:text-base">
                        @if(request('search'))
                            <i class="fas fa-times absolute right-3 top-1/2 transform -translate-y-1/2 text-white/40 cursor-pointer hover:text-white" onclick="clearSearch()"></i>
                        @endif
                    </div>
                </div>
                <div class="w-full md:w-48">
                    <select name="jenis_kelamin" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm md:text-base">
                        <option value="" class="text-black bg-white">Semua Jenis Kelamin</option>
                        <option value="L" class="text-black bg-white" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" class="text-black bg-white" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>
                <div class="w-full md:w-40">
                    <select name="sort_by" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm md:text-base">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama Guru</option>
                        <option value="nip" {{ request('sort_by') == 'nip' ? 'selected' : '' }}>NIP</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg text-white font-semibold text-sm md:text-base">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.guru.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm md:text-base flex items-center justify-center">
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
            <i class="fas fa-times-circle mr-2"></i> Gagal menyimpan data:
        </div>
        <ul class="list-disc list-inside text-xs md:text-sm pl-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table Data Guru -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h6 class="text-white font-semibold text-lg">Daftar Guru</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $gurus->firstItem() ?? 0 }} - {{ $gurus->lastItem() ?? 0 }} dari {{ $gurus->total() }} data</p>
                </div>
                <div>
                    <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-emerald-500/20 rounded-lg text-emerald-400 text-sm md:text-base">
                        <i class="fas fa-plus"></i> Tambah Data Guru
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIP</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Guru</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Email</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">L/P</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No. Telp</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $gurus->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">
                            @if($item->nip)
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-500/20 text-purple-400">
                                    {{ $item->nip }}
                                </span>
                            @else
                                <span class="text-white/30">-</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $item->name }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->email }}</td>
                        <td class="p-3 md:p-4 text-sm">
                            @if($item->jenis_kelamin == 'L')
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">Laki-laki</span>
                            @elseif($item->jenis_kelamin == 'P')
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-pink-500/20 text-pink-400">Perempuan</span>
                            @else
                                <span class="text-white/30">-</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->mapel ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->telp ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-center">
                            <button type="button" 
                                    onclick='openEditModal(@json($item))' 
                                    class="text-blue-400 hover:text-blue-300 mx-1 text-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')" 
                                    class="text-red-400 hover:text-red-300 mx-1 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-chalkboard-teacher text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Tidak ada data guru</p>
                                <button type="button" onclick="openCreateModal()" class="mt-4 px-4 py-2 bg-purple-500/20 rounded-lg text-purple-400">
                                    <i class="fas fa-plus"></i> Tambah Guru
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($gurus->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
            <p class="text-white/40 text-sm">
                Showing {{ $gurus->firstItem() }} to {{ $gurus->lastItem() }} of {{ $gurus->total() }} entries
            </p>
            <div class="flex gap-2">
                {{-- Previous Button --}}
                @if($gurus->onFirstPage())
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $gurus->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-purple-500/20 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($gurus->getUrlRange(1, $gurus->lastPage()) as $page => $url)
                    @if($page == $gurus->currentPage())
                        <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-purple-500/20 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if($gurus->hasMorePages())
                    <a href="{{ $gurus->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-purple-500/20 transition">
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

<!-- Modal Create Guru - Responsive -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-plus-circle text-purple-400 mr-2"></i>
                Tambah Guru
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.guru.store') }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                               placeholder="Contoh: 198203...">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Mata Pelajaran</label>
                        <input type="text" name="mapel"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                               placeholder="Contoh: Matematika">
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Nama Guru <span class="text-red-400">*</span></label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                           placeholder="Nama Lengkap beserta gelar">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                               placeholder="guru@sekolah.com">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Password <span class="text-red-400">*</span></label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                               placeholder="Minimal 4 karakter">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Jenis Kelamin <span class="text-red-400">*</span></label>
                        <select name="jenis_kelamin" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">No. Telp / WA</label>
                        <input type="text" name="telp"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                               placeholder="Contoh: 081234...">
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Alamat</label>
                    <textarea name="alamat" rows="2"
                              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                              placeholder="Alamat lengkap tempat tinggal"></textarea>
                </div>
            </div>
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg text-white font-semibold text-sm flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Guru - Responsive -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-edit text-yellow-400 mr-2"></i>
                Edit Guru
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4 md:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" name="nip" id="edit_nip"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Mata Pelajaran</label>
                        <input type="text" name="mapel" id="edit_mapel"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Nama Guru <span class="text-red-400">*</span></label>
                    <input type="text" name="name" id="edit_name" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" id="edit_email" required
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Password <span class="text-xs text-white/40">(Kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"
                               placeholder="Password baru">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Jenis Kelamin <span class="text-red-400">*</span></label>
                        <select name="jenis_kelamin" id="edit_jenis_kelamin" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">No. Telp / WA</label>
                        <input type="text" name="telp" id="edit_telp"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Alamat</label>
                    <textarea name="alamat" id="edit_alamat" rows="2"
                              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-purple-500 focus:outline-none text-sm"></textarea>
                </div>
            </div>
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg text-white font-semibold text-sm flex items-center gap-2">
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
    function openEditModal(guru) {
        document.getElementById('edit_nip').value = guru.nip || '';
        document.getElementById('edit_mapel').value = guru.mapel || '';
        document.getElementById('edit_name').value = guru.name || '';
        document.getElementById('edit_email').value = guru.email || '';
        document.getElementById('edit_jenis_kelamin').value = guru.jenis_kelamin || '';
        document.getElementById('edit_telp').value = guru.telp || '';
        document.getElementById('edit_alamat').value = guru.alamat || '';
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('admin/guru') }}/" + guru.id;
        
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
        if (confirm(`Apakah Anda yakin ingin menghapus data guru "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('admin/guru') }}/" + id;
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
