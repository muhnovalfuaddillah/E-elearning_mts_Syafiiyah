@extends('layouts.app')
@section('title', 'Dashboard Admin - Pembelajaran Digital')
@section('page-title', 'Dashboard Ringkasan Sekolah')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    @php
        $totalSiswa = \App\Models\Siswa::count();
        $totalGuru = \App\Models\User::where('role', 'guru')->count();
        $totalKelas = \App\Models\Kelas::count();
        $totalMapel = \App\Models\MataPelajaran::count();

        // Rata-rata Kehadiran Umum
        $absenTotal = \App\Models\Absensi::count();
        $absenHadir = \App\Models\Absensi::where('status', 'H')->count();
        $persentaseHadir = $absenTotal > 0 ? ($absenHadir / $absenTotal) * 100 : 100;

        // Distribusi Status
        $absenSakit = \App\Models\Absensi::where('status', 'S')->count();
        $absenIzin = \App\Models\Absensi::where('status', 'I')->count();
        $absenAlpa = \App\Models\Absensi::where('status', 'A')->count();

        // Log Aktivitas Terbaru
        $recentLogs = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        // Kalender Akademik Terdekat
        $upcomingEvents = \App\Models\AcademicCalendar::orderBy('start_date', 'asc')->take(4)->get();
    @endphp

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      
      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Total Siswa</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ $totalSiswa }}</h3>
            <p class="text-purple-400 text-[10px] mt-2 font-semibold"><i class="fas fa-user-graduate"></i> Terdaftar aktif</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-purple-500/20 text-purple-400">
            <i class="fas fa-graduation-cap text-xl"></i>
          </div>
        </div>
      </div>

      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Total Guru</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ $totalGuru }}</h3>
            <p class="text-blue-400 text-[10px] mt-2 font-semibold"><i class="fas fa-chalkboard-teacher"></i> Tenaga Pengajar</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-blue-500/20 text-blue-400">
            <i class="fas fa-chalkboard-teacher text-xl"></i>
          </div>
        </div>
      </div>

      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Total Kelas</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ $totalKelas }}</h3>
            <p class="text-pink-400 text-[10px] mt-2 font-semibold"><i class="fas fa-school"></i> Rombongan Belajar</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-pink-500/20 text-pink-400">
            <i class="fas fa-school text-xl"></i>
          </div>
        </div>
      </div>

      <div class="luxury-card p-5">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Rata Kehadiran</p>
            <h3 class="text-2xl font-bold stat-number mt-1 text-white">{{ number_format($persentaseHadir, 1) }}%</h3>
            <p class="text-emerald-400 text-[10px] mt-2 font-semibold"><i class="fas fa-calendar-check"></i> Kehadiran siswa</p>
          </div>
          <div class="luxury-icon w-12 h-12 bg-emerald-500/20 text-emerald-400">
            <i class="fas fa-calendar-check text-xl animate-pulse"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      
      <!-- Main Chart (Attendance Trend) -->
      <div class="luxury-card p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-3">
          <div>
            <h6 class="text-white font-semibold text-lg">Grafik Trend Kehadiran</h6>
            <p class="text-white/40 text-xs">Menampilkan tingkat kehadiran harian sekolah akhir-akhir ini</p>
          </div>
        </div>
        <div class="h-[250px] relative">
          <canvas id="attendanceTrendChart"></canvas>
        </div>
      </div>

      <!-- Doughnut Chart (Attendance Distribution) -->
      <div class="luxury-card p-6">
        <div class="flex justify-between items-center mb-6 border-b border-white/10 pb-3">
          <h6 class="text-white font-semibold text-lg">Distribusi Absensi</h6>
        </div>
        <div class="h-[200px] relative flex justify-center">
          <canvas id="attendanceDistributionChart"></canvas>
        </div>
        <div class="mt-4 flex justify-around text-center text-xs text-white/50">
          <div>
            <span class="block w-2.5 h-2.5 rounded-full bg-emerald-500 mx-auto"></span>
            <span class="mt-1 block">Hadir</span>
          </div>
          <div>
            <span class="block w-2.5 h-2.5 rounded-full bg-blue-500 mx-auto"></span>
            <span class="mt-1 block">Sakit</span>
          </div>
          <div>
            <span class="block w-2.5 h-2.5 rounded-full bg-yellow-500 mx-auto"></span>
            <span class="mt-1 block">Izin</span>
          </div>
          <div>
            <span class="block w-2.5 h-2.5 rounded-full bg-red-500 mx-auto"></span>
            <span class="mt-1 block">Alpa</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Log Aktivitas & Agenda Akademik -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      
      <!-- Log Aktivitas Terakhir -->
      <div class="luxury-card overflow-hidden">
        <div class="p-6 border-b border-white/10 flex justify-between items-center">
          <h6 class="text-white font-semibold text-lg flex items-center gap-1.5"><i class="fas fa-history text-purple-400"></i> Log Aktivitas Sistem</h6>
          <a href="{{ route('admin.activity-logs.index') }}" class="text-xs text-purple-400 hover:underline">Lihat Semua</a>
        </div>
        <div class="p-4 space-y-3">
          @forelse($recentLogs as $log)
            <div class="flex items-start justify-between p-3 bg-white/5 rounded-xl border border-white/5">
                <div class="flex-1 min-w-0 pr-2">
                    <p class="text-white text-xs font-semibold">{{ $log->user->name ?? 'System' }}</p>
                    <p class="text-white/60 text-[11px] mt-0.5 leading-snug">{{ $log->description }}</p>
                </div>
                <div class="text-right text-[10px] text-white/40 shrink-0">
                    <span>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                </div>
            </div>
          @empty
            <div class="p-8 text-center text-white/40">
                <p class="text-xs">Belum ada aktivitas log tercatat.</p>
            </div>
          @endforelse
        </div>
      </div>

      <!-- Agenda Akademik Terdekat -->
      <div class="luxury-card overflow-hidden">
        <div class="p-6 border-b border-white/10 flex justify-between items-center">
          <h6 class="text-white font-semibold text-lg flex items-center gap-1.5"><i class="fas fa-calendar-alt text-pink-400"></i> Kalender & Agenda Sekolah</h6>
          <a href="{{ route('admin.calendar.index') }}" class="text-xs text-pink-400 hover:underline">Kelola Agenda</a>
        </div>
        <div class="p-4 space-y-3">
          @forelse($upcomingEvents as $event)
            <div class="flex items-center gap-4 p-3 bg-white/5 rounded-xl border border-white/5">
                <div class="shrink-0 w-12 text-center py-1.5 rounded-lg {{ $event->type == 'libur' ? 'bg-red-500/20 text-red-400' : ($event->type == 'ujian' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-purple-500/20 text-purple-400') }}">
                    <span class="block text-[10px] font-bold uppercase">{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</span>
                    <span class="block text-lg font-extrabold">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h6 class="text-white font-semibold text-sm truncate">{{ $event->title }}</h6>
                    <p class="text-white/50 text-xs mt-0.5 truncate">{{ $event->description ?? 'Tidak ada keterangan.' }}</p>
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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 1. Line Chart: Kehadiran Harian 7 Hari Terakhir
    const trendCtx = document.getElementById('attendanceTrendChart');
    if (trendCtx) {
      @php
        // Ambil absensi agregat 7 hari terakhir
        $trendData = \App\Models\Absensi::select('tanggal', 
            \DB::raw('COUNT(*) as total'),
            \DB::raw('SUM(CASE WHEN status = "H" THEN 1 ELSE 0 END) as hadir')
        )
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'desc')
        ->take(7)
        ->get()
        ->reverse();

        $trendLabels = [];
        $trendRates = [];
        foreach ($trendData as $t) {
            $trendLabels[] = \Carbon\Carbon::parse($t->tanggal)->format('d M');
            $trendRates[] = $t->total > 0 ? round(($t->hadir / $t->total) * 100, 1) : 100;
        }
      @endphp

      const trendLabels = {!! json_encode($trendLabels) !!};
      const trendValues = {!! json_encode($trendRates) !!};

      const finalLabels = trendLabels.length > 0 ? trendLabels : ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
      const finalValues = trendValues.length > 0 ? trendValues : [95, 98, 92, 94, 96, 97];

      new Chart(trendCtx, {
        type: 'line',
        data: {
          labels: finalLabels,
          datasets: [{
            label: 'Tingkat Kehadiran (%)',
            data: finalValues,
            backgroundColor: 'rgba(168, 85, 247, 0.1)',
            borderColor: '#a855f7',
            borderWidth: 2.5,
            fill: true,
            tension: 0.35,
            pointRadius: 4,
            pointBackgroundColor: '#ec4899',
            pointBorderColor: '#fff',
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            x: {
              grid: { display: false },
              ticks: { color: 'rgba(255, 255, 255, 0.4)' }
            },
            y: {
              min: 0,
              max: 100,
              grid: { color: 'rgba(255, 255, 255, 0.05)' },
              ticks: { color: 'rgba(255, 255, 255, 0.4)' }
            }
          }
        }
      });
    }

    // 2. Doughnut Chart: Distribusi Absensi Keseluruhan
    const distCtx = document.getElementById('attendanceDistributionChart');
    if (distCtx) {
      const hadirVal = {{ $absenHadir }};
      const sakitVal = {{ $absenSakit }};
      const izinVal = {{ $absenIzin }};
      const alpaVal = {{ $absenAlpa }};

      const finalHadir = (hadirVal + sakitVal + izinVal + alpaVal) > 0 ? hadirVal : 1;
      const finalSakit = sakitVal;
      const finalIzin = izinVal;
      const finalAlpa = alpaVal;

      new Chart(distCtx, {
        type: 'doughnut',
        data: {
          labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
          datasets: [{
            data: [finalHadir, finalSakit, finalIzin, finalAlpa],
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '75%',
          plugins: {
            legend: { display: false }
          }
        }
      });
    }
  });
</script>
@endsection