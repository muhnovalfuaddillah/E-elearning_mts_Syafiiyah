@extends('layouts.app')

@section('title', 'Data Siswa - Pembelajaran Digital')
@section('breadcrumb', 'Siswa')
@section('page-title', 'Data Siswa')

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
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Siswa</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">{{ $siswa->total() }}</h3>
                    <p class="text-blue-400 text-xs md:text-sm mt-2"><i class="fas fa-user-graduate"></i> Aktif</p>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12">
                    <i class="fas fa-users text-white text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Laki-laki (L)</p>
                    <h3 class="text-xl md:text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Siswa::where('jenis_kelamin', 'L')->count() }}
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
                        {{ \App\Models\Siswa::where('jenis_kelamin', 'P')->count() }}
                    </h3>
                </div>
                <div class="luxury-icon w-10 h-10 md:w-12 md:h-12 bg-teal-500/20">
                    <i class="fas fa-venus text-teal-400 text-base md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-3 md:p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Kelas</p>
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
        <form method="GET" action="{{ route('admin.siswa.index') }}" id="searchForm">
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput"
                               placeholder="Cari nama, NIS, atau NISN siswa..." 
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
                        <select name="jenis_kelamin" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="" class="text-black bg-white">Semua Gender</option>
                            <option value="L" class="text-black bg-white" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" class="text-black bg-white" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <select name="sort_by" onchange="document.getElementById('searchForm').submit()" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                            <option value="nama" {{ request('sort_by') == 'nama' ? 'selected' : '' }}>Nama Siswa</option>
                            <option value="nis" {{ request('sort_by') == 'nis' ? 'selected' : '' }}>NIS</option>
                            <option value="kelas" {{ request('sort_by') == 'kelas' ? 'selected' : '' }}>Kelas</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
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

    <!-- Validation Errors (e.g. from Excel import fail) -->
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 max-h-[300px] overflow-y-auto">
        <div class="font-semibold text-sm md:text-base mb-1">
            <i class="fas fa-times-circle mr-2"></i> Terjadi kesalahan pada proses data:
        </div>
        <ul class="list-disc list-inside text-xs md:text-sm pl-2 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table Data Siswa -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h6 class="text-white font-semibold text-lg">Daftar Siswa</h6>
                    <p class="text-white/40 text-sm">Menampilkan {{ $siswa->firstItem() ?? 0 }} - {{ $siswa->lastItem() ?? 0 }} dari {{ $siswa->total() }} data</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="openImportModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm flex items-center gap-2">
                        <i class="fas fa-file-excel"></i> Upload Excel (CSV)
                    </button>
                    <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm flex items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Siswa
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NISN</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">L/P</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No. Telp</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $siswa->firstItem() + $index }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->nis }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->nisn ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $item->nama }}</td>
                        <td class="p-3 md:p-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->kelas->kode_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="p-3 md:p-4 text-sm">
                            @if($item->jenis_kelamin == 'L')
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">Laki-laki</span>
                            @elseif($item->jenis_kelamin == 'P')
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-teal-500/20 text-teal-400">Perempuan</span>
                            @else
                                <span class="text-white/30">-</span>
                            @endif
                        </td>
                        <td class="p-3 md:p-4 text-white/80 text-sm">{{ $item->telp ?? '-' }}</td>
                        <td class="p-3 md:p-4 text-center">
                            <button type="button" 
                                    onclick='openEditModal(@json($item))' 
                                    class="text-blue-400 hover:text-blue-300 mx-1 text-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->nama) }}')" 
                                    class="text-red-400 hover:text-red-300 mx-1 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-users text-6xl text-white/20 mb-4"></i>
                                <p class="text-white/50">Tidak ada data siswa</p>
                                <div class="mt-4 flex gap-2 justify-center">
                                    <button type="button" onclick="openImportModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm">
                                        <i class="fas fa-file-excel"></i> Upload Excel (CSV)
                                    </button>
                                    <button type="button" onclick="openCreateModal()" class="px-4 py-2 bg-blue-500/20 rounded-lg text-blue-400 text-sm">
                                        <i class="fas fa-plus"></i> Tambah Siswa
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($siswa->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center flex-wrap gap-4">
            <p class="text-white/40 text-sm">
                Showing {{ $siswa->firstItem() }} to {{ $siswa->lastItem() }} of {{ $siswa->total() }} entries
            </p>
            <div class="flex gap-2">
                {{-- Previous Button --}}
                @if($siswa->onFirstPage())
                    <button class="px-3 py-1 rounded-lg bg-white/5 text-white/40 text-sm cursor-not-allowed" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $siswa->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($siswa->getUrlRange(1, $siswa->lastPage()) as $page => $url)
                    @if($page == $siswa->currentPage())
                        <a href="#" class="px-3 py-1 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm">{{ $page }}</a>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if($siswa->hasMorePages())
                    <a href="{{ $siswa->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-white/5 text-white/60 text-sm hover:bg-blue-500/20 transition">
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

<!-- Modal Upload Excel (CSV) - Responsive -->
<div id="importModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold flex items-center">
                <i class="fas fa-file-excel text-blue-400 mr-2"></i>
                Upload Data Siswa dari Excel/CSV
            </h3>
            <button type="button" onclick="closeImportModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-4 md:p-6 space-y-4 text-white/80">
                <!-- Panduan Upload -->
                <div class="p-4 bg-white/5 rounded-xl border border-white/10 text-xs md:text-sm space-y-2">
                    <p class="font-semibold text-blue-400"><i class="fas fa-info-circle"></i> Petunjuk Pengisian & Upload:</p>
                    <ol class="list-decimal list-inside space-y-1 text-white/60 pl-1">
                        <li>Unduh template file dengan mengklik tombol <strong>Unduh Template</strong> di bawah ini.</li>
                        <li>Buka file di Microsoft Excel, lalu isi data siswa Anda.</li>
                        <li>Pastikan kolom <strong>kode_kelas</strong> diisi dengan Kode Kelas yang sudah terdaftar di sistem (Contoh: <code class="text-emerald-300">KLS-10-MIPA1</code>).</li>
                        <li>Kolom <strong>jenis_kelamin</strong> hanya boleh diisi huruf <strong class="text-blue-300">L</strong> (Laki-laki) atau <strong class="text-teal-300">P</strong> (Perempuan).</li>
                        <li>Setelah selesai, pilih menu <strong>Save As</strong> di Excel dan pilih format file <strong>CSV (Comma delimited) (*.csv)</strong>.</li>
                        <li>Unggah file CSV tersebut melalui form di bawah ini.</li>
                    </ol>
                    <div class="pt-2">
                        <a href="{{ route('admin.siswa.download-template') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-lg text-xs font-semibold hover:bg-blue-500/30 transition">
                            <i class="fas fa-download"></i> Unduh Template Excel (CSV)
                        </a>
                    </div>
                </div>

                <div>
                    <label class="text-white/75 text-sm block mb-2 font-semibold">Pilih File CSV <span class="text-red-400">*</span></label>
                    <input type="file" name="file" accept=".csv, .txt" required
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white/70 focus:border-blue-500 focus:outline-none text-sm">
                </div>
            </div>
            
            <div class="p-4 md:p-6 border-t border-white/10 flex gap-3 justify-end">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm flex items-center gap-1.5">
                    <i class="fas fa-upload"></i> Mulai Impor Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Create Siswa - Responsive -->
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-plus-circle text-blue-400 mr-2"></i>
                Tambah Siswa
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('admin.siswa.store') }}" method="POST">
            @csrf
            <div class="p-4 md:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">NIS (Nomor Induk Siswa) <span class="text-red-400">*</span></label>
                        <input type="text" name="nis" required
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                               placeholder="Contoh: 202610...">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">NISN (Nasional)</label>
                        <input type="text" name="nisn"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                               placeholder="Contoh: 009876...">
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Nama Siswa <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" required
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Nama Lengkap Siswa">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Pilih Kelas <span class="text-red-400">*</span></label>
                        <select name="kelas_id" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">Jenis Kelamin <span class="text-red-400">*</span></label>
                        <select name="jenis_kelamin" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">No. Telp Siswa / Wali</label>
                    <input type="text" name="telp"
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                           placeholder="Contoh: 081234...">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Alamat</label>
                    <textarea name="alamat" rows="2"
                              class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm"
                              placeholder="Alamat lengkap tinggal siswa"></textarea>
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

<!-- Modal Edit Siswa - Responsive -->
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 overflow-y-auto">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl w-full max-w-[95%] md:max-w-lg mx-4 my-8 shadow-2xl border border-white/10">
        <div class="p-4 md:p-6 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white text-lg md:text-xl font-bold">
                <i class="fas fa-edit text-yellow-400 mr-2"></i>
                Edit Siswa
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
                        <label class="text-white/70 text-sm block mb-1">NIS (Nomor Induk Siswa) <span class="text-red-400">*</span></label>
                        <input type="text" name="nis" id="edit_nis" required
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-white/70 text-sm block mb-1">NISN (Nasional)</label>
                        <input type="text" name="nisn" id="edit_nisn"
                               class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Nama Siswa <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" id="edit_nama" required
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
                        <label class="text-white/70 text-sm block mb-1">Jenis Kelamin <span class="text-red-400">*</span></label>
                        <select name="jenis_kelamin" id="edit_jenis_kelamin" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="L">Laki-laki (L)</option>
                            <option value="P">Perempuan (P)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">No. Telp Siswa / Wali</label>
                    <input type="text" name="telp" id="edit_telp"
                           class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="text-white/70 text-sm block mb-1">Alamat</label>
                    <textarea name="alamat" id="edit_alamat" rows="2"
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

    // Modal Import Excel (CSV)
    function openImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
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
    function openEditModal(siswa) {
        document.getElementById('edit_nis').value = siswa.nis || '';
        document.getElementById('edit_nisn').value = siswa.nisn || '';
        document.getElementById('edit_nama').value = siswa.nama || '';
        document.getElementById('edit_kelas_id').value = siswa.kelas_id || '';
        document.getElementById('edit_jenis_kelamin').value = siswa.jenis_kelamin || '';
        document.getElementById('edit_telp').value = siswa.telp || '';
        document.getElementById('edit_alamat').value = siswa.alamat || '';
        
        const form = document.getElementById('editForm');
        form.action = "{{ url('admin/siswa') }}/" + siswa.id;
        
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
        if (confirm(`Apakah Anda yakin ingin menghapus data siswa "${nama}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url('admin/siswa') }}/" + id;
            form.submit();
        }
    }

    // Close modals when clicking outside
    const importModal = document.getElementById('importModal');
    const createModal = document.getElementById('createModal');
    const editModal = document.getElementById('editModal');
    
    if (importModal) {
        importModal.addEventListener('click', function(e) {
            if (e.target === this) closeImportModal();
        });
    }
    
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
