@extends('layouts.app')

@section('title', 'Hasil Ujian Siswa - Pembelajaran Digital')
@section('breadcrumb', 'Hasil Ujian')
@section('page-title', 'Hasil Nilai Ujian')

@section('content')
<div class="w-full px-4 md:px-6 py-6">
    <!-- Back and Title Info -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <a href="{{ route('guru.ujian.index') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white mb-2 text-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
            </a>
            <h4 class="text-white font-bold text-xl">{{ $ujian->judul }}</h4>
            <p class="text-white/40 text-xs md:text-sm mt-1">
                Mapel: <span class="text-white/80 font-semibold">{{ $ujian->mapel->nama_mapel }}</span> | 
                Kelas: <span class="text-blue-400 font-semibold">{{ $ujian->kelas->kode_kelas }}</span> | 
                Durasi: <span class="text-purple-400 font-semibold">{{ $ujian->durasi }} Menit</span>
            </p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('guru.ujian.export-pdf', $ujian->id) }}" target="_blank" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg text-sm flex items-center gap-2 font-semibold transition">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
            <a href="{{ route('guru.ujian.export-word', $ujian->id) }}" class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg text-sm flex items-center gap-2 font-semibold transition">
                <i class="fas fa-file-word"></i> Unduh Word
            </a>
        </div>
    </div>

    <!-- Table Hasil Ujian -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
            <h6 class="text-white font-semibold text-lg">Daftar Nilai Siswa Kelas {{ $ujian->kelas->nama_kelas }}</h6>
            <p class="text-white/40 text-sm">Menampilkan status pengerjaan dan perolehan nilai siswa.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Mulai Mengerjakan</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Selesai Mengerjakan</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="text-right p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-32">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $index => $siswa)
                        @php
                            $sesi = $ujianSiswas->get($siswa->id);
                        @endphp
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="p-3 md:p-4 text-white/80 text-sm">{{ $index + 1 }}</td>
                            <td class="p-3 md:p-4 text-white/80 text-sm">{{ $siswa->nis }}</td>
                            <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $siswa->nama }}</td>
                            <td class="p-3 md:p-4 text-white/80 text-sm">
                                {{ $sesi ? $sesi->waktu_mulai->format('d M Y, H:i') . ' WIB' : '-' }}
                            </td>
                            <td class="p-3 md:p-4 text-white/80 text-sm">
                                {{ ($sesi && $sesi->waktu_selesai) ? $sesi->waktu_selesai->format('d M Y, H:i') . ' WIB' : '-' }}
                            </td>
                            <td class="p-3 md:p-4 text-sm">
                                @if(!$sesi)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white/5 border border-white/10 text-white/40">Belum Ujian</span>
                                @elseif($sesi->status == 'mengerjakan')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 animate-pulse">Sedang Mengerjakan</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Selesai</span>
                                @endif
                            </td>
                            <td class="p-3 md:p-4 text-right">
                                @if($sesi && $sesi->status == 'selesai')
                                    <span class="text-base font-bold {{ $sesi->nilai >= 75 ? 'text-emerald-400' : 'text-red-400' }}">
                                        {{ number_format($sesi->nilai, 1) }}
                                    </span>
                                @elseif($sesi && $sesi->status == 'mengerjakan')
                                    <span class="text-white/40 text-sm">-</span>
                                @else
                                    <span class="text-white/20 text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-white/40 text-sm">
                                <i class="fas fa-users text-5xl text-white/10 mb-3 block"></i>
                                Tidak ada siswa terdaftar di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
