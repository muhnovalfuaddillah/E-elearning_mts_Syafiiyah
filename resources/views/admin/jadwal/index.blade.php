@extends('layouts.app')

@section('title', 'Jadwal Pelajaran - MTs Syafiiyah')
@section('breadcrumb', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')

<style>
    select option {
        color: black !important;
        background-color: white !important;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Stats & Filters Row -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="luxury-card p-4 md:p-6 flex-1">
            <form method="GET" action="{{ route('admin.jadwal.index') }}" class="m-0 flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <select name="kelas_id" onchange="this.form.submit()" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm md:text-base">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-500/20 border border-blue-500/30 rounded-xl text-blue-400 font-semibold text-sm hover:bg-blue-500/30">
                        <i class="fas fa-filter mr-1.5"></i> Filter
                    </button>
                    @if($selectedKelasId)
                        <a href="{{ route('admin.jadwal.index') }}" class="px-4 py-2 bg-white/5 rounded-xl text-white/70 hover:text-white text-sm flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <button onclick="openCreateModal()" class="px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white font-bold text-sm hover:opacity-90 transition-all shadow-glow shrink-0">
            <i class="fas fa-plus mr-2"></i> Tambah Jadwal Baru
        </button>
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

    <!-- Data Table -->
    <div class="luxury-card overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h6 class="text-white font-semibold text-lg">Jadwal Mingguan</h6>
            <p class="text-white/40 text-sm">Menampilkan jadwal pelajaran terdaftar</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[750px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Hari</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Kelas</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Guru Pengampu</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Waktu</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Ruangan</th>
                        <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal as $item)
                    <tr class="border-b border-white/5 hover:bg-white/5">
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                {{ $item->hari }}
                            </span>
                        </td>
                        <td class="p-4 text-white font-medium text-sm">
                            {{ $item->kelas->kode_kelas }}
                        </td>
                        <td class="p-4 text-white text-sm font-semibold">
                            {{ $item->mapel->nama_mapel }} ({{ $item->mapel->kode_mapel }})
                        </td>
                        <td class="p-4 text-white/80 text-sm">
                            {{ $item->mapel->guru ? $item->mapel->guru->name : 'Belum Ditentukan' }}
                        </td>
                        <td class="p-4 text-white/80 text-sm font-mono">
                            {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                        </td>
                        <td class="p-4 text-white/60 text-sm">
                            {{ $item->ruangan ?? 'Kelas Reguler' }}
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick='openEditModal(@json($item))' class="text-blue-400 hover:text-blue-300 text-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('admin.jadwal.destroy', $item->id) }}" method="POST" class="inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmSweetDelete(this, 'jadwal')" class="text-red-400 hover:text-red-300 text-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-white/40">
                            <i class="fas fa-calendar-alt text-4xl mb-3 block"></i>
                            Belum ada jadwal pelajaran untuk kriteria terpilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== CREATE MODAL ==================== -->
<div id="createModal" class="fixed inset-0 z-[1050] hidden overflow-y-auto bg-black/60 backdrop-blur-sm">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="luxury-card w-full max-w-lg overflow-hidden border border-white/20">
            <div class="flex items-center justify-between p-6 border-b border-white/10">
                <h5 class="text-white font-bold text-lg">Tambah Jadwal Baru</h5>
                <button onclick="closeCreateModal()" class="text-white/50 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.jadwal.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Kelas</label>
                        <select name="kelas_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Mata Pelajaran</label>
                        <select name="mapel_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->nama_mapel }} ({{ $m->guru ? $m->guru->name : 'Tanpa Guru' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Hari</label>
                        <select name="hari" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Jam Mulai</label>
                        <input type="text" name="jam_mulai" required placeholder="07:30" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Jam Selesai</label>
                        <input type="text" name="jam_selesai" required placeholder="09:00" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Ruangan (Opsional)</label>
                    <input type="text" name="ruangan" placeholder="Contoh: R. Laboratorium IPA, R-105" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-white/5 rounded-xl text-white/70 hover:text-white text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all shadow-glow">
                        Simpan
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
                <h5 class="text-white font-bold text-lg">Edit Jadwal Pelajaran</h5>
                <button onclick="closeEditModal()" class="text-white/50 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Kelas</label>
                        <select name="kelas_id" id="edit_kelas_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Mata Pelajaran</label>
                        <select name="mapel_id" id="edit_mapel_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            @foreach($mapels as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Hari</label>
                        <select name="hari" id="edit_hari" required class="w-full px-3 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Jam Mulai</label>
                        <input type="text" name="jam_mulai" id="edit_jam_mulai" required placeholder="07:30" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-white/70 text-sm mb-2 font-medium">Jam Selesai</label>
                        <input type="text" name="jam_selesai" id="edit_jam_selesai" required placeholder="09:00" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Ruangan (Opsional)</label>
                    <input type="text" name="ruangan" id="edit_ruangan" placeholder="Contoh: R. Laboratorium IPA, R-105" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-white/5 rounded-xl text-white/70 hover:text-white text-sm font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all shadow-glow">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(item) {
        document.getElementById('editModal').classList.remove('hidden');
        
        document.getElementById('edit_kelas_id').value = item.kelas_id;
        document.getElementById('edit_mapel_id').value = item.mapel_id;
        document.getElementById('edit_hari').value = item.hari;
        
        // Format time strings (H:i)
        const formatTime = (timeStr) => {
            if (!timeStr) return '';
            const parts = timeStr.split(':');
            return `${parts[0]}:${parts[1]}`;
        };
        
        document.getElementById('edit_jam_mulai').value = formatTime(item.jam_mulai);
        document.getElementById('edit_jam_selesai').value = formatTime(item.jam_selesai);
        document.getElementById('edit_ruangan').value = item.ruangan || '';

        const form = document.getElementById('editForm');
        form.action = `/admin/jadwal/${item.id}`;
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection

