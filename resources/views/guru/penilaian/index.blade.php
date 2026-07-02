@extends('layouts.app')

@section('title', 'Input Penilaian Siswa - Pembelajaran Digital')
@section('breadcrumb', 'Penilaian')
@section('page-title', 'Input Penilaian')

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

    <!-- Search and Filter Section - Responsive -->
    <div class="luxury-card p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('guru.penilaian.index') }}" id="filterForm">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Pilih Kelas</label>
                    <select name="kelas_id" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" class="text-black bg-white" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 w-full">
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Pilih Mata Pelajaran</label>
                    <select name="mapel_id" required class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                        <option value="" class="text-black bg-white">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapels as $m)
                            <option value="{{ $m->id }}" class="text-black bg-white" {{ $selectedMapelId == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-auto shrink-0 flex gap-2">
                    <button type="submit" class="w-full md:w-auto px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-semibold text-sm flex items-center justify-center gap-1.5">
                        <i class="fas fa-filter"></i> Tampilkan Siswa
                    </button>
                    @if($selectedKelasId || $selectedMapelId)
                        <a href="{{ route('guru.penilaian.index') }}" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-sm flex items-center justify-center">
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

    <!-- Main Entry Panel -->
    @if($selectedKelasId && $selectedMapelId)
        <div class="luxury-card overflow-hidden">
            <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                    <div>
                        <h6 class="text-white font-semibold text-lg">Input Nilai Kelas</h6>
                        <p class="text-white/40 text-xs md:text-sm">
                            Mapel: <strong class="text-emerald-300">{{ \App\Models\MataPelajaran::find($selectedMapelId)->nama_mapel }}</strong> | 
                            Kelas: <strong class="text-blue-300">{{ \App\Models\Kelas::find($selectedKelasId)->kode_kelas }}</strong>
                        </p>
                    </div>
                    <div class="p-2.5 bg-blue-500/10 border border-blue-500/20 rounded-xl text-xs text-blue-400 max-w-sm">
                        <i class="fas fa-info-circle"></i> Bobot Nilai Akhir: <strong>Rata Harian 40%</strong>, <strong>UTS 30%</strong>, <strong>UAS 30%</strong>. Nilai Akhir akan dihitung otomatis setelah disimpan.
                    </div>
                </div>
            </div>

            <form action="{{ route('guru.penilaian.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                <input type="hidden" name="mapel_id" value="{{ $selectedMapelId }}">

                <div class="overflow-x-auto">
                    <table class="w-full luxury-table min-w-[950px]">
                        <thead class="border-b border-white/10 bg-white/5">
                            <tr>
                                <th class="text-left p-2 text-white/60 text-xs font-semibold uppercase tracking-wider w-10">No</th>
                                <th class="text-left p-2 text-white/60 text-xs font-semibold uppercase tracking-wider w-24">NIS</th>
                                <th class="text-left p-2 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                                <th class="text-center p-2 text-white/60 text-[11px] font-semibold uppercase tracking-wider w-16">H1</th>
                                <th class="text-center p-2 text-white/60 text-[11px] font-semibold uppercase tracking-wider w-16">H2</th>
                                <th class="text-center p-2 text-white/60 text-[11px] font-semibold uppercase tracking-wider w-16">H3</th>
                                <th class="text-center p-2 text-white/60 text-[11px] font-semibold uppercase tracking-wider w-16">H4</th>
                                <th class="text-center p-2 text-white/60 text-[11px] font-semibold uppercase tracking-wider w-16">H5</th>
                                <th class="text-center p-2 text-white/60 text-[11px] font-semibold uppercase tracking-wider w-16">H6</th>
                                <th class="text-center p-2 text-white/60 text-xs font-semibold uppercase tracking-wider w-20 text-emerald-300 bg-blue-500/5">Rata Harian (40%)</th>
                                <th class="text-center p-2 text-white/60 text-xs font-semibold uppercase tracking-wider w-20">UTS (30%)</th>
                                <th class="text-center p-2 text-white/60 text-xs font-semibold uppercase tracking-wider w-20">UAS (30%)</th>
                                <th class="text-center p-2 text-white/60 text-xs font-semibold uppercase tracking-wider w-20 bg-teal-500/5 text-teal-300">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $index => $item)
                                @php
                                    $studentGrade = $grades->get($item->id);
                                @endphp
                                <tr class="border-b border-white/5 hover:bg-white/5">
                                    <td class="p-2 text-white/80 text-xs">{{ $index + 1 }}</td>
                                    <td class="p-2">
                                        <span class="px-1.5 py-0.5 rounded-lg text-[10px] font-semibold bg-blue-500/20 text-blue-400">
                                            {{ $item->nis }}
                                        </span>
                                    </td>
                                    <td class="p-2 text-white font-medium text-xs">{{ $item->nama }}</td>
                                    
                                    <!-- Input Nilai Harian 1-6 -->
                                    @for($i = 1; $i <= 6; $i++)
                                        @php
                                            $field = 'nilai_harian_' . $i;
                                        @endphp
                                        <td class="p-2 text-center">
                                            <input type="number" 
                                                   name="grades[{{ $item->id }}][nilai_harian_{{ $i }}]" 
                                                   value="{{ old('grades.'.$item->id.'.nilai_harian_'.$i, $studentGrade ? $studentGrade->$field : '') }}"
                                                   min="0" max="100" step="0.01"
                                                   placeholder="-"
                                                   class="w-14 text-center px-1 py-1 bg-white/5 border border-white/10 rounded-lg text-white font-semibold focus:border-blue-500 focus:outline-none text-xs">
                                        </td>
                                    @endfor
                                    
                                    <!-- Rata-rata Nilai Harian (Read-only) -->
                                    <td class="p-2 text-center bg-blue-500/5">
                                        @if($studentGrade && $studentGrade->nilai_harian !== null)
                                            <span class="text-emerald-300 font-bold text-xs">
                                                {{ number_format($studentGrade->nilai_harian, 1) }}
                                            </span>
                                        @else
                                            <span class="text-white/30 italic text-[10px]">-</span>
                                        @endif
                                    </td>

                                    <!-- Input Nilai UTS -->
                                    <td class="p-2 text-center">
                                        <input type="number" 
                                               name="grades[{{ $item->id }}][nilai_uts]" 
                                               value="{{ old('grades.'.$item->id.'.nilai_uts', $studentGrade ? $studentGrade->nilai_uts : '') }}"
                                               min="0" max="100" step="0.01"
                                               placeholder="-"
                                               class="w-16 text-center px-1.5 py-1 bg-white/5 border border-white/10 rounded-lg text-white font-semibold focus:border-blue-500 focus:outline-none text-xs">
                                    </td>
                                    
                                    <!-- Input Nilai UAS -->
                                    <td class="p-2 text-center">
                                        <input type="number" 
                                               name="grades[{{ $item->id }}][nilai_uas]" 
                                               value="{{ old('grades.'.$item->id.'.nilai_uas', $studentGrade ? $studentGrade->nilai_uas : '') }}"
                                               min="0" max="100" step="0.01"
                                               placeholder="-"
                                               class="w-16 text-center px-1.5 py-1 bg-white/5 border border-white/10 rounded-lg text-white font-semibold focus:border-blue-500 focus:outline-none text-xs">
                                    </td>

                                    <!-- Nilai Akhir (Read-only Preview) -->
                                    <td class="p-2 text-center bg-teal-500/5">
                                        @if($studentGrade && $studentGrade->nilai_akhir !== null)
                                            <span class="px-2 py-0.5 rounded-lg text-xs font-bold {{ $studentGrade->nilai_akhir >= 75 ? 'bg-blue-500/20 text-blue-400' : 'bg-red-500/20 text-red-400' }}">
                                                {{ number_format($studentGrade->nilai_akhir, 1) }}
                                            </span>
                                        @else
                                            <span class="text-white/30 italic text-[10px]">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="p-12 text-center text-white/50">
                                        <i class="fas fa-users-slash text-5xl mb-3 text-white/10"></i>
                                        <p>Tidak ada siswa terdaftar di kelas ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswa->isNotEmpty())
                    <div class="p-4 md:p-6 border-t border-white/10 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-sm shadow-glow flex items-center gap-1.5">
                            <i class="fas fa-save"></i> Simpan Semua Nilai
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <!-- Welcome / Instructions Panel -->
        <div class="luxury-card p-8 md:p-12 text-center text-white/80">
            <div class="max-w-md mx-auto space-y-4">
                <div class="luxury-icon w-16 h-16 mx-auto bg-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fas fa-star text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white tracking-tight">Input Penilaian Akademik</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Silakan gunakan panel filter di atas untuk memilih **Kelas** dan **Mata Pelajaran** yang ingin Anda input nilainya.
                </p>
                <div class="p-4 bg-white/5 rounded-xl border border-white/10 text-left text-xs space-y-2">
                    <p class="font-semibold text-blue-400"><i class="fas fa-info-circle"></i> Info Penilaian:</p>
                    <ul class="list-disc list-inside space-y-1 text-white/60 pl-1">
                        <li>Semua nilai diinput dalam skala angka <strong>0 - 100</strong>.</li>
                        <li>Sistem otomatis menghitung Nilai Akhir.</li>
                        <li>Nilai KKM standar kelulusan adalah <strong>75.0</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
