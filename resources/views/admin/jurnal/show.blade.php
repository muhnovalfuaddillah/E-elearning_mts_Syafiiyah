@extends('layouts.app')

@section('title', 'Detail Jurnal Mengajar - Pembelajaran Digital')
@section('breadcrumb', 'Detail Jurnal')
@section('page-title', 'Detail Jurnal Mengajar')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <div class="max-w-2xl mx-auto">

        <!-- Back Link -->
        <a href="{{ route('admin.jurnal.index') }}" class="inline-flex items-center text-blue-400 hover:text-emerald-300 text-sm font-semibold mb-6 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Rekap Jurnal
        </a>

        <!-- Detail Card -->
        <div class="luxury-card overflow-hidden">
            <div class="p-6 border-b border-white/10 bg-white/5 flex justify-between items-center">
                <div>
                    <h5 class="text-white font-bold text-lg"><i class="fas fa-file-alt text-blue-400 mr-2"></i> Detail Pelaksanaan Pembelajaran</h5>
                    <p class="text-white/40 text-xs md:text-sm mt-1">Dibuat oleh Guru: <strong class="text-emerald-300">{{ $jurnal->guru->name }}</strong></p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.jurnal.destroy', $jurnal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini sebagai Administrator?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500/20 border border-red-500/30 text-red-400 text-xs font-semibold rounded-xl hover:bg-red-500/30 transition-all flex items-center gap-1.5">
                            <i class="fas fa-trash"></i> Hapus Jurnal
                        </button>
                    </form>
                </div>
            </div>

            <div class="p-6 space-y-6 text-white/80">
                <!-- Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-white/10 text-sm">
                    <div>
                        <span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Tanggal Mengajar</span>
                        <strong class="text-white text-base">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d F Y') }}</strong>
                    </div>
                    <div>
                        <span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Pertemuan Pembelajaran</span>
                        <strong class="text-emerald-300 text-base">Pertemuan Ke-{{ $jurnal->pertemuan_ke }}</strong>
                    </div>
                    <div>
                        <span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Kelas</span>
                        <strong class="text-white text-base">{{ $jurnal->kelas->nama_lengkap }} ({{ $jurnal->kelas->kode_kelas }})</strong>
                    </div>
                    <div>
                        <span class="text-white/40 block text-xs uppercase tracking-wider mb-0.5">Mata Pelajaran</span>
                        <strong class="text-white text-base">{{ $jurnal->mapel->nama_mapel }}</strong>
                    </div>
                </div>

                <!-- Content blocks -->
                <div class="space-y-4">
                    <div>
                        <span class="text-white/40 block text-xs uppercase tracking-wider mb-1.5">Materi Pokok / Pembahasan</span>
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5 text-sm leading-relaxed whitespace-pre-line text-white">
                            {{ $jurnal->materi }}
                        </div>
                    </div>

                    <div>
                        <span class="text-white/40 block text-xs uppercase tracking-wider mb-1.5">Kegiatan Pembelajaran</span>
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5 text-sm leading-relaxed whitespace-pre-line text-slate-200">
                            {{ $jurnal->kegiatan }}
                        </div>
                    </div>

                    <div>
                        <span class="text-white/40 block text-xs uppercase tracking-wider mb-1.5">Catatan / Evaluasi Mengajar</span>
                        <div class="bg-white/5 p-4 rounded-xl border border-white/5 text-sm leading-relaxed whitespace-pre-line text-slate-300 italic">
                            {{ $jurnal->catatan ? $jurnal->catatan : 'Tidak ada catatan atau hambatan yang dilaporkan.' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-white/10 bg-white/5 flex justify-between items-center text-xs text-white/30">
                <span>Dibuat: {{ $jurnal->created_at->format('d/m/Y H:i') }}</span>
                <span>Terakhir Diupdate: {{ $jurnal->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

    </div>

</div>
@endsection
