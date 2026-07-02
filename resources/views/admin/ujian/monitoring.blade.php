@extends('layouts.app')

@section('title', 'Monitoring Ujian - Pembelajaran Digital')
@section('breadcrumb', 'Monitoring Ujian')
@section('page-title', 'Monitoring Ujian & Soal')

@section('content')
<div class="w-full px-4 md:px-6 py-6 text-white">

    <!-- Header & Search -->
    <div class="luxury-card p-5 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h5 class="text-white font-semibold text-lg">Monitoring Pembuatan Soal Ujian</h5>
                <p class="text-white/40 text-sm">Pantau guru yang sudah membuat soal ujian maupun yang belum melengkapi soal.</p>
            </div>
            <form method="GET" action="{{ route('admin.ujian.monitoring') }}" class="w-full md:w-80">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-white/40"></i>
                    <input type="text" 
                           name="search" 
                           placeholder="Cari nama guru..." 
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-500 focus:outline-none text-sm">
                </div>
            </form>
        </div>
    </div>

    <!-- Guru List Monitoring -->
    <div class="space-y-6">
        @forelse($gurus as $guru)
            <div class="luxury-card overflow-hidden border border-white/5 hover:border-white/10 transition-all">
                <!-- Guru Header Card -->
                <div class="p-5 bg-white/5 border-b border-white/10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-lg font-bold">
                            {{ strtoupper(substr($guru->name, 0, 2)) }}
                        </div>
                        <div>
                            <h6 class="text-white font-bold text-base">{{ $guru->name }}</h6>
                            <p class="text-white/40 text-xs mt-0.5">
                                Mengajar: 
                                @forelse($guru->mataPelajarans as $mp)
                                    <span class="text-white/70">{{ $mp->nama_mapel }}</span>{{ !$loop->last ? ',' : '' }}
                                @empty
                                    <span class="text-white/30 italic">Belum mengaitkan mata pelajaran</span>
                                @endforelse
                            </p>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        @if(!$guru->has_ujian)
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Belum Membuat Ujian
                            </span>
                        @elseif($guru->has_empty_soal)
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                <i class="fas fa-edit mr-1"></i> Ada Ujian Tanpa Soal
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <i class="fas fa-check-circle mr-1"></i> Soal Sudah Lengkap
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Exams List for this Guru -->
                <div class="p-5">
                    @if($guru->ujians->isEmpty())
                        <p class="text-white/40 text-sm italic py-4 text-center">Guru ini belum membuat jadwal ujian apapun di sistem.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-white/10 text-white/40 font-semibold uppercase tracking-wider">
                                        <th class="pb-3 w-12">No</th>
                                        <th class="pb-3">Judul Ujian</th>
                                        <th class="pb-3">Kelas</th>
                                        <th class="pb-3">Mata Pelajaran</th>
                                        <th class="pb-3">Jadwal Ujian</th>
                                        <th class="pb-3">Durasi</th>
                                        <th class="pb-3">Status</th>
                                        <th class="pb-3 text-right">Jumlah Soal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @foreach($guru->ujians as $idx => $uj)
                                        <tr>
                                            <td class="py-3.5 text-white/50">{{ $idx + 1 }}</td>
                                            <td class="py-3.5 font-medium text-white">{{ $uj->judul }}</td>
                                            <td class="py-3.5">
                                                <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 font-mono text-[10px]">
                                                    {{ $uj->kelas->kode_kelas ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 text-white/70">{{ $uj->mapel->nama_mapel ?? '-' }}</td>
                                            <td class="py-3.5 text-white/60">
                                                {{ $uj->waktu_mulai->format('d M Y, H:i') }} - {{ $uj->waktu_selesai->format('H:i') }} WIB
                                            </td>
                                            <td class="py-3.5 text-white/60">{{ $uj->durasi }} Menit</td>
                                            <td class="py-3.5">
                                                @if($uj->status == 'published')
                                                    <span class="text-emerald-400 font-semibold">Aktif</span>
                                                @elseif($uj->status == 'draft')
                                                    <span class="text-yellow-400 font-semibold">Draf</span>
                                                @else
                                                    <span class="text-red-400 font-semibold">Ditutup</span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 text-right">
                                                @if($uj->soals_count == 0)
                                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30">
                                                        0 Soal (Belum Ada)
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                        {{ $uj->soals_count }} Soal
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="luxury-card py-16 text-center text-white/40 text-sm">
                <i class="fas fa-users text-6xl text-white/10 mb-4 block"></i>
                Tidak ada data guru yang ditemukan.
            </div>
        @endforelse
    </div>

</div>
@endsection
