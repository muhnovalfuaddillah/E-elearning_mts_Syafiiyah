@extends('layouts.app')

@section('title', 'Dashboard Guru - Pembelajaran Digital')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard Guru')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    @php
        $teacherId = Auth::id();
        $totalMateri = \App\Models\Materi::where('user_id', $teacherId)->count();
        $totalTugas = \App\Models\Tugas::where('guru_id', $teacherId)->count();
        $totalSiswa = \App\Models\Siswa::count(); // Total siswa sekolah
        $totalKelas = \App\Models\Kelas::count(); // Total kelas

        // Rata-rata Nilai Harian yang diampu oleh Guru ini (bisa difilter jika ada relasi mapel pengampu,
        // namun untuk saat ini, kita ambil rata-rata nilai harian umum di sekolah)
        $rataNilaiTugas = \App\Models\Penilaian::avg('nilai_harian') ?? 0;
        
        // Agenda Akademik Terdekat
        $upcomingEvents = \App\Models\AcademicCalendar::orderBy('start_date', 'asc')->take(5)->get();
    @endphp

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      
      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Materi Saya</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ $totalMateri }}</h3>
            <p class="text-blue-400 text-[10px] mt-2 font-semibold"><i class="fas fa-book"></i> Bahan ajar diupload</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-blue-500/20 text-blue-400">
            <i class="fas fa-book-open text-xl"></i>
          </div>
        </div>
      </div>

      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Tugas Dibuat</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ $totalTugas }}</h3>
            <p class="text-teal-400 text-[10px] mt-2 font-semibold"><i class="fas fa-file-alt"></i> Penugasan aktif</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-teal-500/20 text-teal-400">
            <i class="fas fa-file-alt text-xl"></i>
          </div>
        </div>
      </div>

      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Rombel Kelas</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ $totalKelas }}</h3>
            <p class="text-green-400 text-[10px] mt-2 font-semibold"><i class="fas fa-school"></i> Total Kelas aktif</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-green-500/20 text-green-400">
            <i class="fas fa-school text-xl"></i>
          </div>
        </div>
      </div>

      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Rata Ujian</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ number_format($rataNilaiTugas, 1) }}</h3>
            <p class="text-amber-400 text-[10px] mt-2 font-semibold"><i class="fas fa-star"></i> Poin nilai tugas</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-amber-500/20 text-amber-400">
            <i class="fas fa-star text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Grid: Charts & Agendas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Left: Chart -->
        <div class="lg:col-span-2 space-y-8">
            <div class="luxury-card p-6">
                <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-3">
                    <div>
                        <h6 class="text-white font-semibold text-lg">Statistik Evaluasi Kelas</h6>
                        <p class="text-white/40 text-xs">Menampilkan rata-rata nilai tugas vs UTS vs UAS sekolah</p>
                    </div>
                </div>
                <div class="h-[260px] relative">
                    <canvas id="classEvaluationsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right: Academic Calendar list -->
        <div class="space-y-6">
            <div class="luxury-card p-6">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-white/10">
                    <i class="fas fa-calendar-alt text-amber-400 text-lg"></i>
                    <h5 class="text-white font-bold text-lg">Agenda Sekolah Terdekat</h5>
                </div>
                <div class="space-y-4">
                    @forelse($upcomingEvents as $event)
                        <div class="flex items-center gap-3 p-3 bg-white/5 rounded-xl border border-white/5">
                            <div class="shrink-0 w-12 text-center py-1 rounded-lg {{ $event->type == 'libur' ? 'bg-red-500/20 text-red-400' : ($event->type == 'ujian' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-blue-500/20 text-blue-400') }}">
                                <span class="block text-[9px] font-bold uppercase">{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</span>
                                <span class="block text-base font-extrabold">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h6 class="text-white font-semibold text-xs truncate">{{ $event->title }}</h6>
                                <p class="text-white/50 text-[10px] mt-0.5 truncate">{{ $event->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-white/40">
                            <p class="text-xs">Belum ada agenda akademik terdekat.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('classEvaluationsChart');
        if (!ctx) return;

        @php
            // Ambil rata-rata nilai per komponen harian, uts, uas
            $avgTugas = \App\Models\Penilaian::avg('nilai_harian') ?? 70;
            $avgUts = \App\Models\Penilaian::avg('nilai_uts') ?? 68;
            $avgUas = \App\Models\Penilaian::avg('nilai_uas') ?? 72;
        @endphp

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Rata Harian', 'Ujian Tengah Semester (UTS)', 'Ujian Akhir Semester (UAS)'],
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: [{{ $avgTugas }}, {{ $avgUts }}, {{ $avgUas }}],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.4)', // Emerald
                        'rgba(245, 158, 11, 0.4)',  // Amber
                        'rgba(13, 148, 136, 0.4)'   // Teal
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(13, 148, 136, 1)'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: 'rgba(255, 255, 255, 0.4)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255, 255, 255, 0.4)' }
                    }
                }
            }
        });
    });
</script>
@endsection