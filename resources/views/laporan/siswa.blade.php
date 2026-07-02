@extends('layouts.app')

@section('title', 'Laporan Siswa - MTs Syafiiyah')
@section('breadcrumb', 'Laporan Siswa')
@section('page-title', 'Laporan Siswa')

<style>
    select option {
        color: black !important;
        background-color: white !important;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Filter Card -->
    <div class="luxury-card p-5 mb-6">
        <form method="GET" action="{{ route('admin.laporan.siswa') }}" class="m-0 flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-white/70 text-sm mb-2 font-medium">Pilih Kelas</label>
                <select name="kelas_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-blue-500 focus:outline-none text-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-500/20 border border-blue-500/30 rounded-xl text-blue-400 font-semibold text-sm hover:bg-blue-500/30 transition-all">
                    <i class="fas fa-search mr-1.5"></i> Tampilkan
                </button>
                @if($selectedKelasId)
                    <a href="{{ route('admin.laporan.siswa', ['kelas_id' => $selectedKelasId, 'export' => 'pdf']) }}" target="_blank" class="w-full sm:w-auto px-5 py-2.5 bg-teal-500/20 border border-teal-500/30 rounded-xl text-teal-400 font-semibold text-sm hover:bg-teal-500/35 transition-all text-center">
                        <i class="fas fa-file-pdf mr-1.5"></i> Cetak PDF
                    </a>
                    <a href="{{ route('admin.laporan.siswa', ['kelas_id' => $selectedKelasId, 'export' => 'excel']) }}" class="w-full sm:w-auto px-5 py-2.5 bg-blue-500/20 border border-blue-500/30 rounded-xl text-blue-400 font-semibold text-sm hover:bg-blue-500/35 transition-all text-center">
                        <i class="fas fa-file-excel mr-1.5"></i> Ekspor Excel
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Preview Table -->
    @if($selectedKelasId)
        <div class="luxury-card overflow-hidden">
            <div class="p-6 border-b border-white/10 flex justify-between items-center">
                <div>
                    <h6 class="text-white font-semibold text-lg">Pratinjau Data Siswa</h6>
                    <p class="text-white/40 text-sm">Kelas: {{ $selectedKelas ? $selectedKelas->kode_kelas : '' }}</p>
                </div>
                <span class="px-3 py-1 rounded-xl text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                    {{ $siswa->count() }} Siswa
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full luxury-table min-w-[700px]">
                    <thead class="border-b border-white/10 bg-white/5">
                        <tr>
                            <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No</th>
                            <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS</th>
                            <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NISN</th>
                            <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                            <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">L/P</th>
                            <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No. Telp</th>
                            <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $item)
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="p-4 text-white/60 text-sm">{{ $index + 1 }}</td>
                            <td class="p-4 text-white text-sm font-semibold">{{ $item->nis }}</td>
                            <td class="p-4 text-white/80 text-sm">{{ $item->nisn ?? '-' }}</td>
                            <td class="p-4 text-white font-bold text-sm">{{ $item->nama }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-xs font-bold {{ $item->jenis_kelamin === 'L' ? 'bg-blue-500/10 text-blue-400' : 'bg-teal-500/10 text-teal-400' }}">
                                    {{ $item->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="p-4 text-white/80 text-sm font-mono">{{ $item->telp ?? '-' }}</td>
                            <td class="p-4 text-white/60 text-xs truncate max-w-xs" title="{{ $item->alamat }}">
                                {{ $item->alamat ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-white/40">
                                <i class="fas fa-users text-4xl mb-3 block"></i>
                                Tidak ada siswa terdaftar di kelas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="luxury-card p-10 text-center text-white/30">
            <i class="fas fa-info-circle text-4xl mb-3 block text-blue-500/20"></i>
            Silakan pilih kelas terlebih dahulu untuk melihat pratinjau data laporan siswa.
        </div>
    @endif

</div>
@endsection

