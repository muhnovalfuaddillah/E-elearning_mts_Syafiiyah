@extends('layouts.app')

@section('title', 'Lembar Kerja Ujian - Pembelajaran Digital')
@section('breadcrumb', 'Ujian')
@section('page-title', 'Lembar Kerja Ujian')

<style>
    /* Custom radio styling for premium look */
    .option-container input[type="radio"] {
        display: none;
    }
    .option-container input[type="radio"]:checked + .option-card {
        background-color: rgba(59, 130, 246, 0.15);
        border-color: rgba(59, 130, 246, 0.5);
        color: #fff;
    }
    .option-container input[type="radio"]:checked + .option-card .option-badge {
        background-color: #3b82f6;
        color: #fff;
    }
</style>

@section('content')
<div class="w-full px-4 md:px-6 py-6 text-white select-none">
    
    <!-- Top Header Panel (Sticky on Scroll) -->
    <div class="luxury-card p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-4 border border-white/10 bg-slate-900/85 backdrop-blur-md sticky top-0 z-50">
        <div>
            <h5 class="font-bold text-base md:text-lg text-white">{{ $ujian->judul }}</h5>
            <p class="text-white/40 text-xs mt-0.5">Mapel: {{ $ujian->mapel->nama_mapel }} | Kelas: {{ $ujian->kelas->kode_kelas }}</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Save Status Indicator -->
            <div id="save-indicator" class="text-xs text-white/50 flex items-center gap-1.5 bg-white/5 px-2.5 py-1 rounded-full border border-white/5">
                <i class="fas fa-check-circle text-emerald-400"></i> Semua jawaban tersimpan
            </div>
            <!-- Countdown Timer Widget -->
            <div class="flex items-center gap-2 px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 font-mono text-base md:text-lg font-bold">
                <i class="far fa-clock animate-pulse"></i>
                <span id="countdown">--:--:--</span>
            </div>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left Panel: Question View (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            
            @foreach($ujian->soals as $index => $soal)
                @php
                    $savedJawaban = $jawabans->get($soal->id);
                    $savedChar = $savedJawaban ? $savedJawaban->jawaban : null;
                @endphp
                
                <!-- Question Container -->
                <div id="soal-box-{{ $index + 1 }}" class="soal-box {{ $index === 0 ? '' : 'hidden' }} luxury-card p-6 border border-white/10 space-y-6">
                    <!-- Question Header -->
                    <div class="flex justify-between items-center border-b border-white/5 pb-4">
                        <span class="px-3 py-1 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded-lg text-xs font-bold uppercase tracking-wider">
                            Soal Nomor {{ $index + 1 }}
                        </span>
                    </div>

                    <!-- Question Body -->
                    <div class="text-white text-base leading-relaxed whitespace-pre-line">{!! e($soal->pertanyaan) !!}</div>

                    @if($soal->gambar)
                        <div class="my-4">
                            <img src="{{ asset('storage/' . $soal->gambar) }}" alt="Gambar Soal" class="max-w-full md:max-w-lg rounded-xl border border-white/10 mx-auto">
                        </div>
                    @endif

                    <!-- Options (A-E) -->
                    <div class="space-y-3">
                        <!-- Opsi A -->
                        <label class="option-container block cursor-pointer">
                            <input type="radio" name="jawaban_{{ $soal->id }}" value="A" {{ $savedChar === 'A' ? 'checked' : '' }}
                                   onchange="saveAnswer({{ $soal->id }}, 'A', {{ $index + 1 }})">
                            <div class="option-card p-4 bg-white/5 border border-white/5 rounded-xl flex items-center gap-3.5 hover:bg-white/10 hover:border-white/10 transition-all">
                                <span class="option-badge w-7 h-7 flex-shrink-0 rounded-lg bg-white/5 border border-white/10 text-white/70 flex items-center justify-center font-bold text-xs">A</span>
                                <span class="text-sm text-white/80">{{ $soal->opsi_a }}</span>
                            </div>
                        </label>

                        <!-- Opsi B -->
                        <label class="option-container block cursor-pointer">
                            <input type="radio" name="jawaban_{{ $soal->id }}" value="B" {{ $savedChar === 'B' ? 'checked' : '' }}
                                   onchange="saveAnswer({{ $soal->id }}, 'B', {{ $index + 1 }})">
                            <div class="option-card p-4 bg-white/5 border border-white/5 rounded-xl flex items-center gap-3.5 hover:bg-white/10 hover:border-white/10 transition-all">
                                <span class="option-badge w-7 h-7 flex-shrink-0 rounded-lg bg-white/5 border border-white/10 text-white/70 flex items-center justify-center font-bold text-xs">B</span>
                                <span class="text-sm text-white/80">{{ $soal->opsi_b }}</span>
                            </div>
                        </label>

                        <!-- Opsi C -->
                        <label class="option-container block cursor-pointer">
                            <input type="radio" name="jawaban_{{ $soal->id }}" value="C" {{ $savedChar === 'C' ? 'checked' : '' }}
                                   onchange="saveAnswer({{ $soal->id }}, 'C', {{ $index + 1 }})">
                            <div class="option-card p-4 bg-white/5 border border-white/5 rounded-xl flex items-center gap-3.5 hover:bg-white/10 hover:border-white/10 transition-all">
                                <span class="option-badge w-7 h-7 flex-shrink-0 rounded-lg bg-white/5 border border-white/10 text-white/70 flex items-center justify-center font-bold text-xs">C</span>
                                <span class="text-sm text-white/80">{{ $soal->opsi_c }}</span>
                            </div>
                        </label>

                        <!-- Opsi D -->
                        <label class="option-container block cursor-pointer">
                            <input type="radio" name="jawaban_{{ $soal->id }}" value="D" {{ $savedChar === 'D' ? 'checked' : '' }}
                                   onchange="saveAnswer({{ $soal->id }}, 'D', {{ $index + 1 }})">
                            <div class="option-card p-4 bg-white/5 border border-white/5 rounded-xl flex items-center gap-3.5 hover:bg-white/10 hover:border-white/10 transition-all">
                                <span class="option-badge w-7 h-7 flex-shrink-0 rounded-lg bg-white/5 border border-white/10 text-white/70 flex items-center justify-center font-bold text-xs">D</span>
                                <span class="text-sm text-white/80">{{ $soal->opsi_d }}</span>
                            </div>
                        </label>

                        <!-- Opsi E (jika ada) -->
                        @if($soal->opsi_e)
                        <label class="option-container block cursor-pointer">
                            <input type="radio" name="jawaban_{{ $soal->id }}" value="E" {{ $savedChar === 'E' ? 'checked' : '' }}
                                   onchange="saveAnswer({{ $soal->id }}, 'E', {{ $index + 1 }})">
                            <div class="option-card p-4 bg-white/5 border border-white/5 rounded-xl flex items-center gap-3.5 hover:bg-white/10 hover:border-white/10 transition-all">
                                <span class="option-badge w-7 h-7 flex-shrink-0 rounded-lg bg-white/5 border border-white/10 text-white/70 flex items-center justify-center font-bold text-xs">E</span>
                                <span class="text-sm text-white/80">{{ $soal->opsi_e }}</span>
                            </div>
                        </label>
                        @endif
                    </div>

                    <!-- Navigation Buttons inside Workspace -->
                    <div class="flex justify-between items-center pt-4 border-t border-white/5">
                        <button type="button" onclick="navigateSoal('prev')" id="btn-prev-{{ $index + 1 }}"
                                class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition {{ $index === 0 ? 'opacity-30 cursor-not-allowed' : '' }}"
                                {{ $index === 0 ? 'disabled' : '' }}>
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>
                        
                        @if($index + 1 === $ujian->soals->count())
                            <button type="button" onclick="submitExam()"
                                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-90 rounded-xl text-xs font-bold tracking-wide transition flex items-center gap-1.5 shadow-glow">
                                <i class="fas fa-check-double"></i> Selesai Ujian
                            </button>
                        @else
                            <button type="button" onclick="navigateSoal('next')" id="btn-next-{{ $index + 1 }}"
                                    class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 border border-blue-500/10 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Panel: Question Navigation (1/3 width) -->
        <div class="space-y-6">
            <div class="luxury-card p-5 border border-white/10">
                <h6 class="text-white font-bold text-sm mb-4"><i class="fas fa-th-large text-blue-400 mr-1.5"></i> Navigasi Soal</h6>
                
                <!-- Grid of numbers -->
                <div class="grid grid-cols-5 gap-2.5">
                    @foreach($ujian->soals as $index => $soal)
                        @php
                            $savedJawaban = $jawabans->get($soal->id);
                            $isAnswered = !is_null($savedJawaban) && !is_null($savedJawaban->jawaban);
                        @endphp
                        <button type="button" id="nav-num-{{ $index + 1 }}" onclick="goToSoal({{ $index + 1 }})"
                                class="nav-btn w-full py-2.5 text-xs font-bold rounded-xl border transition-all flex items-center justify-center 
                                {{ $index === 0 ? 'border-blue-500 text-white bg-blue-500/15' : ($isAnswered ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400' : 'border-white/5 bg-white/5 text-white/50 hover:bg-white/10') }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="mt-6 pt-5 border-t border-white/5 flex flex-col gap-2">
                    <button type="button" onclick="submitExam()"
                            class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-90 rounded-xl text-xs font-bold tracking-wide transition flex items-center justify-center gap-1.5 shadow-glow">
                        <i class="fas fa-check-double"></i> Selesai Ujian
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Form Submit Tersembunyi -->
<form id="form-selesai-ujian" action="{{ route('siswa.ujian.selesai', $ujian->id) }}" method="POST" class="hidden">
    @csrf
</form>

<script>
    let currentSoal = 1;
    const totalSoal = {{ $ujian->soals->count() }};
    let remainingSeconds = {{ $sisaDetik }};

    // 1. Countdown Timer
    function startTimer() {
        const timerElement = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            if (remainingSeconds <= 0) {
                clearInterval(interval);
                timerElement.innerText = "WAKTU HABIS";
                // Auto submit
                alert("Waktu ujian Anda telah habis! Sistem akan mengirim lembar jawaban Anda secara otomatis.");
                document.getElementById('form-selesai-ujian').submit();
                return;
            }

            remainingSeconds--;

            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;

            const hDisplay = hours.toString().padStart(2, '0');
            const mDisplay = minutes.toString().padStart(2, '0');
            const sDisplay = seconds.toString().padStart(2, '0');

            timerElement.innerText = `${hDisplay}:${mDisplay}:${sDisplay}`;

            // Warn if under 5 minutes (300 seconds)
            if (remainingSeconds < 300) {
                timerElement.parentElement.classList.remove('bg-red-500/10', 'text-red-400', 'border-red-500/20');
                timerElement.parentElement.classList.add('bg-red-600', 'text-white', 'animate-pulse');
            }
        }, 1000);
    }

    // 2. Navigasi Soal
    function goToSoal(num) {
        // Sembunyikan soal lama
        document.getElementById(`soal-box-${currentSoal}`).classList.add('hidden');
        
        // Perbarui gaya nomor navigasi lama (pastikan nilainya disesuaikan berdasarkan isi)
        const oldNav = document.getElementById(`nav-num-${currentSoal}`);
        const oldRadio = document.querySelector(`input[name="jawaban_${getSoalIdFromIndex(currentSoal)}"]:checked`);
        
        updateNavBtnStyle(currentSoal, !!oldRadio);

        // Tampilkan soal baru
        document.getElementById(`soal-box-${num}`).classList.remove('hidden');
        
        // Tandai nomor navigasi baru sebagai aktif (highlight border)
        const newNav = document.getElementById(`nav-num-${num}`);
        newNav.className = `nav-btn w-full py-2.5 text-xs font-bold rounded-xl border transition-all flex items-center justify-center border-blue-500 text-white bg-blue-500/20`;

        currentSoal = num;
    }

    function navigateSoal(dir) {
        if (dir === 'prev' && currentSoal > 1) {
            goToSoal(currentSoal - 1);
        } else if (dir === 'next' && currentSoal < totalSoal) {
            goToSoal(currentSoal + 1);
        }
    }

    function updateNavBtnStyle(num, isAnswered) {
        const btn = document.getElementById(`nav-num-${num}`);
        if (isAnswered) {
            btn.className = `nav-btn w-full py-2.5 text-xs font-bold rounded-xl border transition-all flex items-center justify-center border-emerald-500/30 bg-emerald-500/10 text-emerald-400`;
        } else {
            btn.className = `nav-btn w-full py-2.5 text-xs font-bold rounded-xl border transition-all flex items-center justify-center border-white/5 bg-white/5 text-white/50 hover:bg-white/10`;
        }
    }

    function getSoalIdFromIndex(index) {
        // Array of soal IDs to assist mapping
        const map = [
            @foreach($ujian->soals as $s)
                {{ $s->id }},
            @endforeach
        ];
        return map[index - 1];
    }

    // 3. Simpan Jawaban ke Database via AJAX (Real-time)
    function saveAnswer(soalId, jawaban, num) {
        const indicator = document.getElementById('save-indicator');
        indicator.innerHTML = '<i class="fas fa-spinner fa-spin text-blue-400"></i> Menyimpan...';
        indicator.className = 'text-xs text-blue-400 flex items-center gap-1.5 bg-white/5 px-2.5 py-1 rounded-full border border-white/5';

        // Update Navigasi Soal langsung ke warna Hijau
        updateNavBtnStyle(num, true);

        fetch("{{ route('siswa.ujian.simpan-jawaban', $ujian->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                soal_id: soalId,
                jawaban: jawaban
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                indicator.innerHTML = '<i class="fas fa-check-circle text-emerald-400"></i> Semua jawaban tersimpan';
                indicator.className = 'text-xs text-white/50 flex items-center gap-1.5 bg-white/5 px-2.5 py-1 rounded-full border border-white/5';
            } else {
                indicator.innerHTML = '<i class="fas fa-exclamation-circle text-red-400"></i> Gagal menyimpan';
                indicator.className = 'text-xs text-red-400 flex items-center gap-1.5 bg-white/5 px-2.5 py-1 rounded-full border border-white/5';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            indicator.innerHTML = '<i class="fas fa-exclamation-circle text-red-400"></i> Gangguan Koneksi';
            indicator.className = 'text-xs text-red-400 flex items-center gap-1.5 bg-white/5 px-2.5 py-1 rounded-full border border-white/5';
        });
    }

    // 4. Submit Ujian Selesai
    function submitExam() {
        if (confirm('Apakah Anda yakin ingin menyelesaikan ujian ini? Setelah selesai, jawaban tidak dapat diubah kembali.')) {
            document.getElementById('form-selesai-ujian').submit();
        }
    }

    // Start timer on load
    window.onload = startTimer;
</script>
@endsection
