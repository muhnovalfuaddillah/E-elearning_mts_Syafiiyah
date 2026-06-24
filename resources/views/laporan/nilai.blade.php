@extends('layouts.app')

@section('title', 'Rekap Nilai - MTs Syafiiyah')
@section('breadcrumb', 'Rekap Nilai')
@section('page-title', 'Rekap Nilai')

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
        <form method="GET" action="{{ route(auth()->user()->role . '.laporan.nilai') }}" class="m-0 flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Pilih Kelas</label>
                    <select name="kelas_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-white/70 text-sm mb-2 font-medium">Pilih Mata Pelajaran</label>
                    <select name="mapel_id" required class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:border-purple-500 focus:outline-none text-sm">
                        <option value="">-- Pilih Mapel --</option>
                        <option value="all" {{ $selectedMapelId == 'all' ? 'selected' : '' }}>-- Semua Mata Pelajaran (Rekap Kelas) --</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" {{ $selectedMapelId == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }} ({{ $m->guru ? $m->guru->name : 'Tanpa Guru' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-purple-500/20 border border-purple-500/30 rounded-xl text-purple-400 font-semibold text-sm hover:bg-purple-500/30 transition-all">
                    <i class="fas fa-search mr-1.5"></i> Tampilkan
                </button>
                @if($selectedKelasId && $selectedMapelId)
                    <a href="{{ route(auth()->user()->role . '.laporan.nilai', ['kelas_id' => $selectedKelasId, 'mapel_id' => $selectedMapelId, 'export' => 'pdf']) }}" target="_blank" class="w-full md:w-auto px-5 py-2.5 bg-pink-500/20 border border-pink-500/30 rounded-xl text-pink-400 font-semibold text-sm hover:bg-pink-500/35 transition-all text-center">
                        <i class="fas fa-file-pdf mr-1.5"></i> Cetak PDF
                    </a>
                    <a href="{{ route(auth()->user()->role . '.laporan.nilai', ['kelas_id' => $selectedKelasId, 'mapel_id' => $selectedMapelId, 'export' => 'excel']) }}" class="w-full md:w-auto px-5 py-2.5 bg-emerald-500/20 border border-emerald-500/30 rounded-xl text-emerald-400 font-semibold text-sm hover:bg-emerald-500/35 transition-all text-center">
                        <i class="fas fa-file-excel mr-1.5"></i> Ekspor Excel
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Preview Table -->
    @if($selectedKelasId && $selectedMapelId)
        @if($selectedMapelId === 'all')
            <div class="luxury-card overflow-hidden">
                <div class="p-6 border-b border-white/10 flex justify-between items-center flex-wrap gap-3">
                    <div>
                        <h6 class="text-white font-semibold text-lg">Pratinjau Rekapitulasi Nilai Seluruh Mapel</h6>
                        <p class="text-white/40 text-sm">Kelas: {{ $selectedKelas ? $selectedKelas->kode_kelas : '' }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[850px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                                <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-28">NIS</th>
                                <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                                @foreach($allMapels as $m)
                                    <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">{{ $m->nama_mapel }}</th>
                                @endforeach
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider bg-purple-500/10 text-purple-300">Rata-rata</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider bg-pink-500/10 text-pink-300">Rank Kelas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $index => $item)
                            @php
                                $studentGrades = $grades->get($item->id, collect());
                                $avg = $averages[$item->id] ?? 0;
                                $rank = $classRanks[$item->id] ?? '-';
                            @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5">
                                <td class="p-4 text-white/60 text-sm">{{ $index + 1 }}</td>
                                <td class="p-4 text-white text-sm font-semibold">{{ $item->nis }}</td>
                                <td class="p-4 text-white font-bold text-sm">{{ $item->nama }}</td>
                                @foreach($allMapels as $m)
                                    @php
                                        $g = $studentGrades->firstWhere('mapel_id', $m->id);
                                    @endphp
                                    <td class="p-4 text-center text-white/80 text-sm font-mono">{{ $g ? ($g->nilai_akhir ?? '-') : '-' }}</td>
                                @endforeach
                                <td class="p-4 text-center bg-purple-500/5 text-purple-300 font-bold font-mono">{{ round($avg, 1) }}</td>
                                <td class="p-4 text-center bg-pink-500/5 text-pink-300 font-bold font-mono">#{{ $rank }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 5 + count($allMapels) }}" class="p-8 text-center text-white/40">
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
            <div class="luxury-card overflow-hidden">
                <div class="p-6 border-b border-white/10 flex justify-between items-center flex-wrap gap-3">
                    <div>
                        <h6 class="text-white font-semibold text-lg">Pratinjau Rekapitulasi Nilai Siswa</h6>
                        <p class="text-white/40 text-sm">Mata Pelajaran: {{ $selectedMapel ? $selectedMapel->nama_mapel : '' }}</p>
                    </div>
                    <div class="px-3 py-1 rounded-xl text-xs font-semibold bg-purple-500/10 border border-purple-500/20 text-purple-400">
                        Guru Pengampu: {{ $selectedMapel && $selectedMapel->guru ? $selectedMapel->guru->name : 'Belum ditentukan' }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[750px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">No</th>
                                <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS</th>
                                <th class="text-left p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-48">Rata Harian (40%)</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nilai UTS (30%)</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider text-purple-300">Rank UTS</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nilai UAS (30%)</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider text-purple-300">Rank UAS</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider bg-purple-500/10 text-purple-300">Nilai Akhir</th>
                                <th class="text-center p-4 text-white/60 text-xs font-semibold uppercase tracking-wider bg-pink-500/10 text-pink-300">Rank Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $index => $item)
                            @php
                                $grade = $grades->get($item->id);
                                $harian = $grade ? $grade->nilai_harian : null;
                                $uts = $grade ? $grade->nilai_uts : null;
                                $uas = $grade ? $grade->nilai_uas : null;
                                
                                $nilaiAkhir = '-';
                                if ($harian !== null || $uts !== null || $uas !== null) {
                                    $nilaiAkhir = round(($harian ?? 0) * 0.4 + ($uts ?? 0) * 0.3 + ($uas ?? 0) * 0.3);
                                }

                                $rankUts = $utsRanks[$item->id] ?? '-';
                                $rankUas = $uasRanks[$item->id] ?? '-';
                                $rankFinal = $finalRanks[$item->id] ?? '-';
                            @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5">
                                <td class="p-4 text-white/60 text-sm">{{ $index + 1 }}</td>
                                <td class="p-4 text-white text-sm font-semibold">{{ $item->nis }}</td>
                                <td class="p-4 text-white font-bold text-sm">{{ $item->nama }}</td>
                                <td class="p-4 text-center text-white/80 text-sm font-mono">
                                    <div class="font-bold text-white">{{ $harian !== null ? number_format($harian, 1) : '-' }}</div>
                                    <div class="text-[9px] text-white/40 mt-1 flex flex-wrap justify-center gap-1">
                                        <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 1">H1: {{ ($grade && $grade->nilai_harian_1 !== null) ? round($grade->nilai_harian_1) : '-' }}</span>
                                        <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 2">H2: {{ ($grade && $grade->nilai_harian_2 !== null) ? round($grade->nilai_harian_2) : '-' }}</span>
                                        <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 3">H3: {{ ($grade && $grade->nilai_harian_3 !== null) ? round($grade->nilai_harian_3) : '-' }}</span>
                                        <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 4">H4: {{ ($grade && $grade->nilai_harian_4 !== null) ? round($grade->nilai_harian_4) : '-' }}</span>
                                        <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 5">H5: {{ ($grade && $grade->nilai_harian_5 !== null) ? round($grade->nilai_harian_5) : '-' }}</span>
                                        <span class="px-1 py-0.5 bg-white/5 rounded border border-white/5" title="Harian 6">H6: {{ ($grade && $grade->nilai_harian_6 !== null) ? round($grade->nilai_harian_6) : '-' }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-center text-white/80 text-sm font-mono">{{ $uts ?? '-' }}</td>
                                <td class="p-4 text-center text-purple-300 text-sm font-bold font-mono">#{{ $rankUts }}</td>
                                <td class="p-4 text-center text-white/80 text-sm font-mono">{{ $uas ?? '-' }}</td>
                                <td class="p-4 text-center text-purple-300 text-sm font-bold font-mono">#{{ $rankUas }}</td>
                                <td class="p-4 text-center bg-purple-500/5">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono 
                                        @if($nilaiAkhir !== '-' && $nilaiAkhir >= 80) bg-emerald-500/20 text-emerald-400
                                        @elseif($nilaiAkhir !== '-' && $nilaiAkhir >= 70) bg-blue-500/20 text-blue-400
                                        @elseif($nilaiAkhir !== '-') bg-red-500/20 text-red-400
                                        @else bg-white/5 text-white/40
                                        @endif">
                                        {{ $nilaiAkhir }}
                                    </span>
                                </td>
                                <td class="p-4 text-center bg-pink-500/5 text-pink-300 text-sm font-bold font-mono">#{{ $rankFinal }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="p-8 text-center text-white/40">
                                    <i class="fas fa-users text-4xl mb-3 block"></i>
                                    Tidak ada siswa terdaftar di kelas ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @else
        <div class="luxury-card p-10 text-center text-white/30">
            <i class="fas fa-info-circle text-4xl mb-3 block text-purple-500/20"></i>
            Silakan pilih kelas dan mata pelajaran terlebih dahulu untuk melihat pratinjau rekap nilai.
        </div>
    @endif

</div>
@endsection

