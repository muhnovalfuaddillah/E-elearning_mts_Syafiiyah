@extends('layouts.app')

@section('title', 'Manajemen Pengumuman - MTs Syafiiyah')
@section('breadcrumb', 'Pengumuman')
@section('page-title', 'Pengumuman')

<style>
    select option {
        color: black !important;
        background-color: white !important;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8">
        <div class="luxury-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Total Pengumuman</p>
                    <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ $pengumuman->total() }}</h3>
                    <p class="text-indigo-400 text-xs mt-2"><i class="fas fa-bullhorn"></i> Aktif</p>
                </div>
                <div class="luxury-icon w-12 h-12">
                    <i class="fas fa-bullhorn text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Tipe Sekolah</p>
                    <h3 class="text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Pengumuman::where('tipe', 'sekolah')->count() }}
                    </h3>
                    <p class="text-emerald-400 text-xs mt-2"><i class="fas fa-globe"></i> Untuk Semua</p>
                </div>
                <div class="luxury-icon w-12 h-12 bg-emerald-500/20">
                    <i class="fas fa-globe text-emerald-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="luxury-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider">Tipe Kelas</p>
                    <h3 class="text-2xl font-bold stat-number mt-1 text-white">
                        {{ \App\Models\Pengumuman::where('tipe', 'kelas')->count() }}
                    </h3>
                    <p class="text-pink-400 text-xs mt-2"><i class="fas fa-chalkboard"></i> Spesifik Kelas</p>
                </div>
                <div class="luxury-icon w-12 h-12 bg-pink-500/20">
                    <i class="fas fa-users text-pink-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-lg text-emerald-400 flex items-center justify-between">
        <div class="text-sm">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 flex items-center justify-between">
        <div class="text-sm">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400">
        <p class="font-semibold mb-1">Terjadi kesalahan validasi:</p>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table Section -->
    <div class="luxury-card overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h6 class="text-white font-semibold text-lg">Daftar Pengumuman</h6>
                    <p class="text-white/40 text-sm">Kelola pengumuman sekolah atau kelas di sini</p>
                </div>
                <div>
                    <button onclick="openCreateModal()" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all shadow-glow">
                        <i class="fas fa-plus mr-2"></i> Tambah Pengumuman
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[700px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Judul</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Tipe</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Target Kelas</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Pembuat</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengumuman as $index => $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-4 text-white/80 text-sm">{{ $pengumuman->firstItem() + $index }}</td>
                        <td class="p-4">
                            <a href="{{ route('announcements.show-detail', $item->id) }}" class="text-white font-medium hover:text-purple-400 transition-colors text-sm">
                                {{ $item->judul }}
                            </a>
                        </td>
                        <td class="p-4">
                            @if($item->tipe === 'sekolah')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/20 text-emerald-400">
                                    Sekolah
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-pink-500/20 text-pink-400">
                                    Kelas
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-white/80 text-sm">
                            {{ $item->kelas ? $item->kelas->kode_kelas : '-' }}
                        </td>
                        <td class="p-4 text-white/80 text-sm">
                            {{ $item->user->name }} ({{ ucfirst($item->user->role) }})
                        </td>
                        <td class="p-4 text-white/60 text-sm">
                            {{ $item->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="p-4 text-center">
                            @if(auth()->user()->role === 'admin' || $item->user_id === auth()->id())
                            <div class="flex justify-center gap-2">
                                <button onclick='openEditModal(@json($item))' class="text-blue-400 hover:text-blue-300 text-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route(auth()->user()->role . '.pengumuman.destroy', $item->id) }}" method="POST" class="inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmSweetDelete(this, 'pengumuman')" class="text-red-400 hover:text-red-300 text-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                            @else
                            <span class="text-white/30 text-xs">No Access</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-white/40">
                            <i class="fas fa-bullhorn text-4xl mb-3 block"></i>
                            Belum ada data pengumuman yang dipublikasikan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengumuman->hasPages())
        <div class="p-4 border-t border-white/10">
            {{ $pengumuman->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ==================== CREATE MODAL ==================== -->
<div id="createModal" class="fixed inset-0 z-[1050] hidden overflow-y-auto bg-black/60 backdrop-blur-sm">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="luxury-card w-full max-w-lg overflow-hidden border border-white/20">
            <div class="flex items-center justify-between p-6 border-b border-white/10">
                <h5 class="text-white font-bold text-lg">Tambah Pengumuman Baru</h5>
                <button onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route(auth()->user()->role . '.pengumuman.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Judul Pengumuman</label>
                    <input type="text" name="judul" required placeholder="Contoh: Libur Semester Ganjil" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Tipe Pengumuman</label>
                        <select name="tipe" id="create_tipe" onchange="toggleCreateKelasSelect()" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                            @if(auth()->user()->role === 'admin')
                                <option value="sekolah">Sekolah (Semua)</option>
                            @endif
                            <option value="kelas">Kelas (Spesifik)</option>
                        </select>
                    </div>

                    <div id="create_kelas_container">
                        <label class="block text-white/70 text-sm mb-2 font-medium">Pilih Kelas</label>
                        <select name="kelas_id" id="create_kelas_id" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->kode_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Isi Pengumuman</label>
                    <textarea name="isi" rows="6" required placeholder="Tuliskan detail isi pengumuman..." class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-xl text-white/70 hover:text-white text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all shadow-glow">
                        Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== EDIT MODAL ==================== -->
<div id="editModal" class="fixed inset-0 z-[1050] hidden overflow-y-auto bg-black/60 backdrop-blur-sm">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="luxury-card w-full max-w-lg overflow-hidden border border-white/20">
            <div class="flex items-center justify-between p-6 border-b border-white/10">
                <h5 class="text-white font-bold text-lg">Edit Pengumuman</h5>
                <button onclick="closeEditModal()" class="text-white/50 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Judul Pengumuman</label>
                    <input type="text" name="judul" id="edit_judul" required placeholder="Contoh: Libur Semester Ganjil" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Tipe Pengumuman</label>
                        <select name="tipe" id="edit_tipe" onchange="toggleEditKelasSelect()" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                            @if(auth()->user()->role === 'admin')
                                <option value="sekolah">Sekolah (Semua)</option>
                            @endif
                            <option value="kelas">Kelas (Spesifik)</option>
                        </select>
                    </div>

                    <div id="edit_kelas_container">
                        <label class="block text-white/70 text-sm mb-2 font-medium">Pilih Kelas</label>
                        <select name="kelas_id" id="edit_kelas_id" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->kode_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Isi Pengumuman</label>
                    <textarea name="isi" id="edit_isi" rows="6" required placeholder="Tuliskan detail isi pengumuman..." class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white/5 rounded-xl text-white/70 hover:text-white text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all shadow-glow">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const userRole = "{{ auth()->user()->role }}";

    // Manage Create Modal
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        toggleCreateKelasSelect();
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function toggleCreateKelasSelect() {
        const tipe = document.getElementById('create_tipe').value;
        const container = document.getElementById('create_kelas_container');
        const select = document.getElementById('create_kelas_id');
        
        if (tipe === 'sekolah') {
            container.style.display = 'none';
            select.removeAttribute('required');
            select.value = '';
        } else {
            container.style.display = 'block';
            select.setAttribute('required', 'required');
        }
    }

    // Manage Edit Modal
    function openEditModal(item) {
        document.getElementById('editModal').classList.remove('hidden');
        
        // Set values
        document.getElementById('edit_judul').value = item.judul;
        document.getElementById('edit_tipe').value = item.tipe;
        document.getElementById('edit_isi').value = item.isi;
        
        // Dynamically toggle
        toggleEditKelasSelect();
        
        if (item.tipe === 'kelas') {
            document.getElementById('edit_kelas_id').value = item.kelas_id;
        }

        // Set action URL
        const form = document.getElementById('editForm');
        form.action = `/${userRole}/pengumuman/${item.id}`;
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function toggleEditKelasSelect() {
        const tipe = document.getElementById('edit_tipe').value;
        const container = document.getElementById('edit_kelas_container');
        const select = document.getElementById('edit_kelas_id');
        
        if (tipe === 'sekolah') {
            container.style.display = 'none';
            select.removeAttribute('required');
            select.value = '';
        } else {
            container.style.display = 'block';
            select.setAttribute('required', 'required');
        }
    }
</script>
@endsection

