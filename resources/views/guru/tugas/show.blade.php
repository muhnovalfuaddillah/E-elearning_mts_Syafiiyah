@extends('layouts.app')

@section('title', 'Detail Pengumpulan Tugas - Pembelajaran Digital')
@section('breadcrumb')
<a href="{{ route('guru.tugas.index') }}" class="hover:text-blue-400">Tugas</a> / Detail
@endsection
@section('page-title', 'Detail Pengumpulan Tugas')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('guru.tugas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-white text-sm font-semibold transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Tugas
        </a>
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

    <!-- Task Detail Info Card -->
    <div class="luxury-card p-6 mb-8 bg-gradient-to-r from-slate-900/60 to-slate-950/60 border-blue-500/20">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-white/10 pb-4 mb-4">
            <div>
                <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-teal-500/20 text-teal-400 border border-teal-500/30">
                    {{ $tugas->mapel->nama_mapel ?? 'Mapel' }}
                </span>
                <span class="ml-2 px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                    Kelas {{ $tugas->kelas->nama_kelas ?? '-' }}
                </span>
                <h3 class="text-white font-extrabold text-2xl mt-2 leading-snug">{{ $tugas->judul }}</h3>
            </div>
            
            <div class="text-right">
                <span class="text-white/40 text-xs block uppercase tracking-wider font-semibold">Tenggat Waktu</span>
                <span class="text-sm font-bold {{ $tugas->deadline->isFuture() ? 'text-blue-400' : 'text-rose-400' }}">
                    {{ $tugas->deadline->format('d M Y, H:i') }} WIB
                    @if($tugas->deadline->isPast())
                        <span class="block text-[10px] text-rose-500/80 mt-1 uppercase font-bold">[ Waktu Habis ]</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <h6 class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-1">Petunjuk / Deskripsi Tugas:</h6>
                <div class="text-white/80 text-sm whitespace-pre-line bg-white/5 p-4 rounded-xl border border-white/5">
                    {{ $tugas->deskripsi }}
                </div>
            </div>

            @if($tugas->file_tugas)
                <div class="flex items-center gap-2">
                    <span class="text-white/40 text-xs font-semibold">File Soal Tugas:</span>
                    <a href="{{ asset('storage/' . $tugas->file_tugas) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-blue-400 hover:text-blue-300 font-semibold hover:underline">
                        <i class="fas fa-file-download"></i> Unduh Lampiran Soal (.{{ pathinfo($tugas->file_tugas, PATHINFO_EXTENSION) }})
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Submissions Table Card -->
    <div class="luxury-card overflow-hidden">
        <div class="p-4 md:p-6 border-b border-white/10 bg-white/5 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h6 class="text-white font-semibold text-lg">Daftar Pengumpulan Siswa</h6>
                <p class="text-white/40 text-xs md:text-sm">Berikut adalah seluruh siswa terdaftar di kelas dan status tugasnya.</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <div class="flex items-center gap-1.5 text-blue-400">
                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                    <span>Sudah: {{ $submissions->count() }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-white/45">
                    <span class="w-2.5 h-2.5 bg-white/10 rounded-full"></span>
                    <span>Belum: {{ max(0, $students->count() - $submissions->count()) }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full luxury-table min-w-[800px]">
                <thead class="border-b border-white/10 bg-white/5">
                    <tr>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Nama Siswa</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">NIS / NISN</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">Waktu Kumpul</th>
                        <th class="text-left p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider">File submit</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-20">Nilai</th>
                        <th class="text-center p-3 md:p-4 text-white/60 text-xs font-semibold uppercase tracking-wider w-36">Aksi / Penilaian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        @php
                            $submission = $submissions->get($student->id);
                            $hasSubmitted = !is_null($submission);
                            $isLate = $hasSubmitted && \Carbon\Carbon::parse($submission->updated_at)->greaterThan($tugas->deadline);
                        @endphp
                        <tr class="border-b border-white/5 hover:bg-white/5">
                            <td class="p-3 md:p-4 text-white/80 text-sm">{{ $index + 1 }}</td>
                            <td class="p-3 md:p-4 text-white font-medium text-sm">{{ $student->nama }}</td>
                            <td class="p-3 md:p-4 text-white/60 text-xs">
                                <div>{{ $student->nis ?? '-' }}</div>
                                <div class="text-[10px] text-white/30">{{ $student->nisn ?? '' }}</div>
                            </td>
                            <td class="p-3 md:p-4 text-xs">
                                @if($hasSubmitted)
                                    @if($isLate)
                                        <span class="px-2 py-0.5 rounded bg-red-500/20 text-red-400 border border-red-500/30">Terlambat</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30">Tepat Waktu</span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded bg-white/5 text-white/40 border border-white/10">Belum Mengumpulkan</span>
                                @endif
                            </td>
                            <td class="p-3 md:p-4 text-white/80 text-xs">
                                @if($hasSubmitted)
                                    <div>{{ \Carbon\Carbon::parse($submission->updated_at)->format('d M Y') }}</div>
                                    <div class="text-white/40 text-[10px]">{{ \Carbon\Carbon::parse($submission->updated_at)->format('H:i') }} WIB</div>
                                @else
                                    <span class="text-white/20">-</span>
                                @endif
                            </td>
                            <td class="p-3 md:p-4 text-xs">
                                @if($hasSubmitted && $submission->file_submit)
                                    <div class="flex flex-col gap-1">
                                        <a href="{{ asset('storage/' . $submission->file_submit) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-400 hover:text-blue-300 font-semibold hover:underline">
                                            <i class="fas fa-file-download"></i> Unduh File
                                        </a>
                                        @if($submission->catatan)
                                            <span class="text-[10px] text-white/45 italic truncate max-w-[150px]" title="{{ $submission->catatan }}">"{{ $submission->catatan }}"</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-white/20">-</span>
                                @endif
                            </td>
                            <td class="p-3 md:p-4 text-sm font-bold text-center">
                                @if($hasSubmitted)
                                    @if(!is_null($submission->nilai))
                                        <span class="text-blue-400 bg-blue-500/10 px-2 py-1 rounded border border-blue-500/20">{{ $submission->nilai }}</span>
                                    @else
                                        <span class="text-yellow-400 bg-yellow-500/10 px-2 py-1 rounded border border-yellow-500/20 italic text-xs">Belum</span>
                                    @endif
                                @else
                                    <span class="text-white/20">-</span>
                                @endif
                            </td>
                            <td class="p-3 md:p-4 text-center">
                                @if($hasSubmitted)
                                    <button type="button" 
                                            onclick="openGradeModal({{ $submission->id }}, '{{ addslashes($student->nama) }}', {{ $submission->nilai ?? 'null' }}, '{{ addslashes($submission->feedback ?? '') }}')"
                                            class="px-3 py-1 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 border border-blue-500/30 rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 mx-auto transition shadow-glow">
                                        <i class="fas fa-award"></i> {{ is_null($submission->nilai) ? 'Beri Nilai' : 'Ubah Nilai' }}
                                    </button>
                                @else
                                    <button class="px-3 py-1 bg-white/5 text-white/25 border border-white/5 rounded-lg text-xs cursor-not-allowed mx-auto" disabled>
                                        <i class="fas fa-ban"></i> Beri Nilai
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-white/50">
                                <i class="fas fa-users-slash text-6xl text-white/10 mb-4"></i>
                                <p class="text-sm">Tidak ada siswa terdaftar di kelas ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== GRADING MODAL ==================== -->
<div id="gradeModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeGradeModal()"></div>
    
    <!-- Modal Box -->
    <div class="relative bg-gradient-to-br from-slate-900 to-slate-950 border border-white/10 rounded-2xl w-full max-w-md p-6 overflow-hidden">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10">
            <h5 class="text-white font-bold text-lg"><i class="fas fa-award text-blue-400 mr-1.5"></i> Penilaian Tugas</h5>
            <button onclick="closeGradeModal()" class="text-white/40 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        
        <form id="gradeForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="text-white/50 text-xs block mb-1 uppercase tracking-wider font-semibold">Nama Siswa:</label>
                    <div class="text-white font-bold text-sm" id="modal-student-name"></div>
                </div>

                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Nilai Tugas (0-100) <span class="text-red-400">*</span></label>
                    <input type="number" name="nilai" id="modal_nilai" required min="0" max="100" placeholder="Contoh: 85"
                           class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="text-white/70 text-xs block mb-1 uppercase tracking-wider font-semibold">Catatan Guru / Feedback (Opsional)</label>
                    <textarea name="feedback" id="modal_feedback" rows="3" placeholder="Tulis masukan atau koreksi tugas di sini..."
                              class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-white/10 flex justify-end gap-2">
                <button type="button" onclick="closeGradeModal()" class="px-4 py-2 bg-white/5 rounded-lg text-white/70 hover:text-white text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg text-white font-bold text-xs shadow-glow">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openGradeModal(submissionId, studentName, currentNilai, currentFeedback) {
        document.getElementById('gradeModal').style.display = 'flex';
        document.getElementById('modal-student-name').innerText = studentName;
        document.getElementById('modal_nilai').value = currentNilai !== null ? currentNilai : '';
        document.getElementById('modal_feedback').value = currentFeedback;
        
        // Set dynamic action route for the grade form
        const form = document.getElementById('gradeForm');
        form.action = "{{ url('guru/tugas/nilai') }}/" + submissionId;
    }

    function closeGradeModal() {
        document.getElementById('gradeModal').style.display = 'none';
    }
</script>
@endsection
