@extends('layouts.app')

@section('title', 'Jurnal Mengajar - Pembelajaran Digital')
@section('breadcrumb', 'Jurnal Mengajar')
@section('page-title', 'Jurnal Mengajar Guru')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Action Bar & Filter -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <div>
            <h6 class="text-white font-semibold text-lg">Riwayat Jurnal Mengajar Anda</h6>
            <p class="text-white/40 text-xs md:text-sm">Dokumentasikan seluruh aktivitas pembelajaran kelas Anda.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="openRekapModal()" class="px-5 py-2.5 bg-blue-500/20 border border-blue-500/30 rounded-xl text-blue-400 font-bold text-sm hover:bg-blue-500/30 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-file-pdf"></i> Rekap Jurnal (PDF)
            </button>
            <a href="{{ route('guru.jurnal.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white font-bold text-sm shadow-glow flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> Buat Jurnal Baru
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('guru.jurnal.index') }}" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Filter Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" class="text-black bg-white" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Filter Mata Pelajaran</label>
                    <select name="mapel_id" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Mapel</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" class="text-black bg-white" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Filter Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-500/20 border border-blue-500/30 rounded-lg text-blue-400 font-semibold text-sm hover:bg-blue-500/30 transition-all flex items-center justify-center gap-1.5">
                        <i class="fas fa-filter"></i> Terapkan
                    </button>
                    @if(request('kelas_id') || request('mapel_id') || request('tanggal'))
                        <a href="{{ route('guru.jurnal.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
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

    <!-- Data Table -->
    <div class="luxury-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-28">Tanggal</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Kelas</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">Pertemuan</th>
                        <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Materi Pokok</th>
                        <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnals as $index => $item)
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="p-4 text-white/80 text-sm">{{ $jurnals->firstItem() + $index }}</td>
                            <td class="p-4 text-white/80 text-sm font-semibold">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-500/20 text-blue-400">
                                    {{ $item->kelas->kode_kelas }}
                                </span>
                            </td>
                            <td class="p-4 text-white font-medium text-sm">{{ $item->mapel->nama_mapel }}</td>
                            <td class="p-4 text-center text-white/80 text-sm font-bold font-mono">Ke-{{ $item->pertemuan_ke }}</td>
                            <td class="p-4 text-white/80 text-sm max-w-xs truncate" title="{{ $item->materi }}">{{ $item->materi }}</td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="showJurnalDetail({{ json_encode($item) }}, '{{ $item->kelas->nama_lengkap }}', '{{ $item->mapel->nama_mapel }}')" class="px-2.5 py-1 bg-blue-500/20 border border-blue-500/30 text-blue-400 text-xs rounded-lg hover:bg-blue-500/30 transition-all">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    <a href="{{ route('guru.jurnal.edit', $item->id) }}" class="px-2.5 py-1 bg-yellow-500/20 border border-yellow-500/30 text-yellow-400 text-xs rounded-lg hover:bg-yellow-500/30 transition-all">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('guru.jurnal.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-red-500/20 border border-red-500/30 text-red-400 text-xs rounded-lg hover:bg-red-500/30 transition-all">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-white/40">
                                <i class="fas fa-file-signature text-5xl mb-3 text-white/10 block"></i>
                                <p>Belum ada jurnal mengajar yang terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jurnals->hasPages())
        <div class="p-4 border-t border-white/10 flex justify-between items-center">
            <p class="text-white/40 text-xs sm:text-sm">
                Showing {{ $jurnals->firstItem() }} to {{ $jurnals->lastItem() }} of {{ $jurnals->total() }} entries
            </p>
            <div>
                {{ $jurnals->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

</div>

<!-- Modal Detail Jurnal -->
<div id="detailModal" class="fixed inset-0 z-[1100] flex items-center justify-center hidden p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative w-full max-w-lg luxury-card overflow-hidden z-10">
        <div class="p-4 md:p-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
            <h5 class="text-white font-bold text-lg"><i class="fas fa-file-alt text-blue-400 mr-2"></i> Detail Jurnal Mengajar</h5>
            <button onclick="closeModal()" class="text-white/50 hover:text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto text-white/80">
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-white/5 text-sm">
                <div>
                    <span class="text-white/40 block text-xs uppercase">Tanggal</span>
                    <strong id="modal-tanggal" class="text-white"></strong>
                </div>
                <div>
                    <span class="text-white/40 block text-xs uppercase">Pertemuan Ke</span>
                    <strong id="modal-pertemuan" class="text-emerald-300"></strong>
                </div>
                <div>
                    <span class="text-white/40 block text-xs uppercase">Kelas</span>
                    <strong id="modal-kelas" class="text-white"></strong>
                </div>
                <div>
                    <span class="text-white/40 block text-xs uppercase">Mata Pelajaran</span>
                    <strong id="modal-mapel" class="text-white"></strong>
                </div>
            </div>
            <div>
                <span class="text-white/40 block text-xs uppercase mb-1">Materi Pokok / Pembahasan</span>
                <p id="modal-materi" class="bg-white/5 p-3 rounded-xl border border-white/5 text-sm whitespace-pre-line"></p>
            </div>
            <div>
                <span class="text-white/40 block text-xs uppercase mb-1">Kegiatan Pembelajaran</span>
                <p id="modal-kegiatan" class="bg-white/5 p-3 rounded-xl border border-white/5 text-sm whitespace-pre-line"></p>
            </div>
            <div>
                <span class="text-white/40 block text-xs uppercase mb-1">Catatan Hambatan / Kelas</span>
                <p id="modal-catatan" class="bg-white/5 p-3 rounded-xl border border-white/5 text-sm whitespace-pre-line italic text-white/60"></p>
            </div>
        </div>
        <div class="p-4 border-t border-white/10 bg-white/5 flex justify-end">
            <button onclick="closeModal()" class="px-5 py-2 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 text-white text-sm font-semibold">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Rekap Jurnal -->
<div id="rekapModal" class="fixed inset-0 z-[1100] flex items-center justify-center hidden p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRekapModal()"></div>
    <div class="relative w-full max-w-md luxury-card overflow-hidden z-10">
        <div class="p-4 md:p-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
            <h5 class="text-white font-bold text-lg"><i class="fas fa-file-pdf text-blue-400 mr-2"></i> Rekap Jurnal Mengajar</h5>
            <button onclick="closeRekapModal()" class="text-white/50 hover:text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('guru.jurnal.rekap') }}" method="GET" target="_blank" onsubmit="closeRekapModal()">
            <div class="p-6 space-y-4 text-white/80">
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Pilih Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" class="text-black bg-white">{{ $k->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Pilih Mata Pelajaran</label>
                    <select name="mapel_id" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">Semua Mapel</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" class="text-black bg-white">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                    </div>
                </div>
                <p class="text-[10px] text-white/40 italic mt-2"><i class="fas fa-info-circle"></i> Catatan: Lembar rekap PDF yang dihasilkan akan membuka dialog cetak browser secara otomatis.</p>
            </div>
            <div class="p-4 border-t border-white/10 bg-white/5 flex justify-end gap-2">
                <button type="button" onclick="closeRekapModal()" class="px-5 py-2 bg-white/5 rounded-xl border border-white/10 hover:bg-white/10 text-white text-sm font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white text-sm font-bold shadow-glow flex items-center gap-1.5">
                    <i class="fas fa-print"></i> Cetak Rekap PDF
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showJurnalDetail(jurnal, kelasName, mapelName) {
        document.getElementById('modal-tanggal').innerText = new Date(jurnal.tanggal).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
        document.getElementById('modal-pertemuan').innerText = 'Ke-' + jurnal.pertemuan_ke;
        document.getElementById('modal-kelas').innerText = kelasName;
        document.getElementById('modal-mapel').innerText = mapelName;
        document.getElementById('modal-materi').innerText = jurnal.materi;
        document.getElementById('modal-kegiatan').innerText = jurnal.kegiatan;
        document.getElementById('modal-catatan').innerText = jurnal.catatan ? jurnal.catatan : 'Tidak ada catatan khusus.';

        const modal = document.getElementById('detailModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openRekapModal() {
        const modal = document.getElementById('rekapModal');
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeRekapModal() {
        const modal = document.getElementById('rekapModal');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>
@endsection
