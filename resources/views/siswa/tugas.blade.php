@extends('layouts.app')

@section('title', 'Tugas Mandiri - Pembelajaran Digital')
@section('breadcrumb', 'Tugas')
@section('page-title', 'Tugas Mandiri')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

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

    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10 bg-white/5">
            <h6 class="text-white font-semibold text-lg">Daftar Tugas Kelas Anda</h6>
            <p class="text-white/40 text-xs md:text-sm">Perhatikan batas akhir pengumpulan (deadline) tugas Anda di bawah ini.</p>
        </div>

        <div class="p-6">
            @if($tugas->isEmpty())
                <div class="py-16 text-center text-white/50">
                    <i class="fas fa-tasks text-6xl text-white/10 mb-4"></i>
                    <p class="text-sm">Hore! Belum ada tugas kelas yang diunggah.</p>
                </div>
            @else
                @php
                    $groupedTugas = $tugas->groupBy(function($item) {
                        return $item->mapel->nama_mapel ?? 'Lainnya';
                    });
                @endphp

                @foreach($groupedTugas as $mapelName => $items)
                    <div class="mb-10 last:mb-0">
                        <!-- Header Mapel -->
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-1.5 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></span>
                            <h5 class="text-white font-bold text-lg tracking-wide">{{ $mapelName }}</h5>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-white/60">
                                {{ $items->count() }} Tugas
                            </span>
                        </div>

                        <!-- Grid Tugas per Mapel -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($items as $item)
                                @php
                                    $submission = $submissions->get($item->id);
                                    $hasSubmitted = !is_null($submission);
                                    $isOverdue = \Carbon\Carbon::now()->greaterThan($item->deadline);
                                @endphp
                                <div class="luxury-card p-5 hover:border-blue-500/30 transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start gap-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-teal-500/20 text-teal-400 border border-teal-500/30">
                                                {{ $item->mapel->kode_mapel ?? 'Mapel' }}
                                            </span>
                                            @if($hasSubmitted)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                                    Sudah Dikumpulkan
                                                </span>
                                            @elseif($isOverdue)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30">
                                                    Terlambat (Selesai)
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">
                                                    Belum Dikumpulkan
                                                </span>
                                            @endif
                                        </div>

                                        <h6 class="text-white font-bold text-base mt-4 leading-snug">{{ $item->judul }}</h6>
                                        <p class="text-white/50 text-xs mt-2 leading-relaxed line-clamp-3">{{ $item->deskripsi }}</p>

                                        @if($item->file_tugas)
                                            <div class="mt-3">
                                                <a href="{{ asset('storage/' . $item->file_tugas) }}" target="_blank" download class="inline-flex items-center gap-1.5 text-xs text-blue-400 hover:text-blue-300 hover:underline font-medium">
                                                    <i class="fas fa-file-download"></i> Lampiran Soal Tugas (.{{ pathinfo($item->file_tugas, PATHINFO_EXTENSION) }})
                                                </a>
                                            </div>
                                        @endif

                                        <div class="mt-4 p-3 bg-white/5 rounded-xl border border-white/5 space-y-1 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-white/40">Diterbitkan:</span>
                                                <span class="text-white/80 font-medium">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }} WIB</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-white/40">Batas Akhir:</span>
                                                <span class="font-semibold {{ $isOverdue ? 'text-red-400' : 'text-blue-400' }}">
                                                    {{ \Carbon\Carbon::parse($item->deadline)->format('d M Y, H:i') }} WIB
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Panel Pengumpulan -->
                                    <div class="mt-6 pt-4 border-t border-white/5 space-y-3">
                                        @if($hasSubmitted)
                                            <div class="p-3 bg-blue-500/5 border border-blue-500/10 rounded-xl space-y-2 text-xs">
                                                <div class="flex justify-between text-white/50">
                                                    <span>File Tugas Saya:</span>
                                                    <a href="{{ asset('storage/' . $submission->file_submit) }}" target="_blank" class="text-blue-400 hover:underline truncate max-w-[180px] font-medium">
                                                        Unduh Pengumpulan
                                                    </a>
                                                </div>
                                                @if($submission->catatan)
                                                    <p class="text-white/60 italic text-[11px]">"{{ $submission->catatan }}"</p>
                                                @endif
                                                @if($submission->nilai)
                                                    <div class="flex justify-between pt-2 border-t border-white/5">
                                                        <span class="text-white/50">Nilai Tugas:</span>
                                                        <span class="text-blue-400 font-bold text-sm">{{ $submission->nilai }}</span>
                                                    </div>
                                                @endif
                                                @if($submission->feedback)
                                                    <p class="text-[11px] text-yellow-400/80"><strong class="text-white/60">Catatan Guru:</strong> {{ $submission->feedback }}</p>
                                                @endif
                                            </div>
                                        @endif

                                        @if(!$isOverdue)
                                            <button onclick="openSubmitModal({{ $item->id }}, '{{ addslashes($item->judul) }}')" class="w-full py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 rounded-lg text-white font-semibold text-xs transition flex items-center justify-center gap-1.5 shadow-glow">
                                                <i class="fas fa-upload"></i> {{ $hasSubmitted ? 'Perbarui Pengumpulan' : 'Kumpulkan Tugas' }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- ==================== SUBMIT TUGAS MODAL ==================== -->
<div id="submitModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeSubmitModal()"></div>
    
    <!-- Modal Box -->
    <div class="relative bg-gradient-to-br from-slate-900 to-slate-950 border border-white/10 rounded-2xl w-full max-w-md p-6 overflow-hidden">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10">
            <h5 class="text-white font-bold text-lg"><i class="fas fa-upload text-blue-400 mr-1.5"></i> Pengumpulan Tugas</h5>
            <button onclick="closeSubmitModal()" class="text-white/40 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="submitTugasForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="text-white/50 text-xs block mb-1 uppercase tracking-wider font-semibold">Tugas:</label>
                    <div class="text-white font-semibold text-sm" id="modal-tugas-title"></div>
                </div>

                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">File Hasil Pekerjaan (Max 5MB)</label>
                    <input type="file" name="file_submit" required 
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                    <p class="text-[10px] text-white/40 mt-1">Diterima: PDF, ZIP, RAR, Word (doc/docx), atau Gambar (PNG/JPG).</p>
                </div>

                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider">Catatan Pendukung (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Tulis catatan pengerjaan di sini..."
                              class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-white/10 flex justify-end gap-2">
                <button type="button" onclick="closeSubmitModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-xs shadow-glow">
                    Kirim Tugas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSubmitModal(id, title) {
        document.getElementById('submitModal').style.display = 'flex';
        document.getElementById('modal-tugas-title').innerHTML = title;
        
        // Dynamic route action setting
        const form = document.getElementById('submitTugasForm');
        form.action = "{{ url('siswa/tugas/submit') }}/" + id;
    }

    function closeSubmitModal() {
        document.getElementById('submitModal').style.display = 'none';
    }
</script>
@endsection
