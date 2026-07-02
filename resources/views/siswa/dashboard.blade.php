@extends('layouts.app')

@section('title', 'Dashboard Siswa - Pembelajaran Digital')
@section('breadcrumb', 'Dashboard')
@section('page-title', 'Dashboard Siswa')

@section('content')
<div class="w-full px-4 md:px-6 py-6">

    <!-- Welcome Card -->
    <div class="luxury-card p-6 mb-8 relative overflow-hidden bg-gradient-to-r from-emerald-900/40 to-teal-900/40 border border-blue-500/20">
        <div class="absolute right-0 top-0 translate-x-10 -translate-y-10 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30 uppercase tracking-wider">
                    Siswa Aktif
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold text-white mt-3">
                    Selamat Datang Kembali, <span class="gradient-text">{{ $siswa->nama }}</span>!
                </h2>
                <p class="text-white/60 text-sm mt-1">
                    Anda terdaftar di kelas <strong class="text-teal-300">{{ $kelas->nama_lengkap }}</strong>. Berikut adalah ikhtisar akademik Anda hari ini.
                </p>
            </div>
            
            <!-- Quick QR Scan Trigger Button -->
            <div>
                <button onclick="openScannerModal()" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-teal-500 rounded-xl text-white font-bold text-sm shadow-glow flex items-center gap-2 hover:translate-y-[-2px] transition-all">
                    <i class="fas fa-qrcode text-lg"></i> Scan QR Absensi Kelas
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <!-- Kehadiran -->
        <div class="luxury-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Tingkat Kehadiran</p>
                    <h3 class="text-2xl font-bold stat-number mt-1 text-white">
                        {{ number_format($persentaseKehadiran, 1) }}%
                    </h3>
                    <p class="text-white/40 text-xs mt-2">
                        <span class="text-blue-400 font-semibold">{{ $hadir }} Hadir</span> | {{ $sakit + $izin }} S/I | {{ $alpa }} Alpa
                    </p>
                </div>
                <div class="luxury-icon w-12 h-12 bg-blue-500/20 text-blue-400">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Rata-rata Nilai -->
        <div class="luxury-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Rata-rata Nilai Akhir</p>
                    <h3 class="text-2xl font-bold stat-number mt-1 text-white">
                        {{ number_format($rataNilai, 1) }}
                    </h3>
                    <p class="text-white/40 text-xs mt-2">
                        Mata Pelajaran yang dinilai
                    </p>
                </div>
                <div class="luxury-icon w-12 h-12 bg-yellow-500/20 text-yellow-400">
                    <i class="fas fa-star text-xl animate-pulse"></i>
                </div>
            </div>
        </div>

        <!-- Tugas Aktif -->
        <div class="luxury-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-wider font-semibold">Tugas Aktif</p>
                    <h3 class="text-2xl font-bold stat-number mt-1 text-white">
                        {{ $tugasAktif }}
                    </h3>
                    <p class="text-white/40 text-xs mt-2">
                        Harus segera dikerjakan
                    </p>
                </div>
                <div class="luxury-icon w-12 h-12 bg-blue-500/20 text-blue-400">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Calendar & Announcements -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Kalender & Grafik Row -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Akademik Kalender -->
            <div class="luxury-card p-6">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10">
                    <h5 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-amber-400"></i> Kalender Akademik
                    </h5>
                    <span class="text-xs text-white/40">Agenda Kegiatan Sekolah</span>
                </div>
                <!-- Mini Agenda Lists -->
                <div class="space-y-4">
                    @forelse($calendarEvents as $event)
                        <div class="flex items-start gap-4 p-3 bg-white/5 rounded-xl border border-white/5 hover:border-blue-500/30 transition">
                            <div class="shrink-0 w-12 text-center py-1 rounded-lg {{ $event->type == 'libur' ? 'bg-red-500/20 text-red-400' : ($event->type == 'ujian' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-blue-500/20 text-blue-400') }}">
                                <span class="block text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($event->start_date)->format('M') }}</span>
                                <span class="block text-lg font-extrabold">{{ \Carbon\Carbon::parse($event->start_date)->format('d') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h6 class="text-white font-semibold text-sm truncate">{{ $event->title }}</h6>
                                <p class="text-white/50 text-xs mt-0.5 truncate">{{ $event->description ?? 'Tidak ada deskripsi agenda.' }}</p>
                            </div>
                            <div class="shrink-0 self-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $event->type == 'libur' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : ($event->type == 'ujian' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30') }}">
                                    {{ $event->type }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-white/40">
                            <i class="fas fa-calendar-times text-4xl mb-2 text-white/10"></i>
                            <p class="text-sm">Tidak ada agenda terdekat yang dijadwalkan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Grafik Kemajuan Akademik -->
            <div class="luxury-card p-6">
                <div class="flex justify-between items-center mb-6 pb-3 border-b border-white/10">
                    <h5 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fas fa-chart-bar text-blue-400"></i> Statistik Nilai Saya
                    </h5>
                    <span class="text-xs text-white/40">Visualisasi Nilai Akhir Mapel</span>
                </div>
                <div class="relative w-full h-[250px]">
                    <canvas id="studentGradesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Pengumuman Sekolah -->
        <div class="space-y-6">
            <div class="luxury-card p-6">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-white/10">
                    <i class="fas fa-bullhorn text-amber-400 text-lg"></i>
                    <h5 class="text-white font-bold text-lg">Pengumuman Terkini</h5>
                </div>
                <div class="space-y-4">
                    @foreach($announcements as $ann)
                        <div class="p-4 bg-white/5 rounded-xl border border-white/5 hover:border-amber-500/20 transition-all">
                            <div class="flex justify-between items-start gap-2">
                                <h6 class="text-white font-semibold text-sm leading-tight">{{ $ann['title'] }}</h6>
                                <span class="text-[10px] text-white/30 whitespace-nowrap">{{ $ann['date'] }}</span>
                            </div>
                            <p class="text-white/60 text-xs mt-2 leading-relaxed">{{ $ann['content'] }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[10px] text-amber-400 font-semibold"><i class="fas fa-user-tie"></i> {{ $ann['author'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ==================== SCANNER QR MODAL ==================== -->
<div id="scannerModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="closeScannerModal()"></div>
    
    <!-- Modal Box -->
    <div class="relative bg-gradient-to-br from-slate-900 to-slate-950 border border-white/10 rounded-2xl w-full max-w-md p-6 overflow-hidden">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-white/10">
            <h5 class="text-white font-bold text-lg"><i class="fas fa-qrcode text-blue-400 mr-1.5"></i> Scan Absensi QR</h5>
            <button onclick="closeScannerModal()" class="text-white/40 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        
        <!-- Video Camera Area -->
        <div class="w-full aspect-square bg-black rounded-xl overflow-hidden relative border border-white/10" id="qr-reader-container">
            <div id="qr-reader" class="w-full h-full"></div>
            <!-- Laser Scanner Line Animation -->
            <div class="absolute inset-x-0 top-0 h-0.5 bg-blue-500/80 animate-bounce pointer-events-none shadow-[0_0_10px_#3b82f6]"></div>
        </div>
        
        <!-- Manual Token Entry Option -->
        <div class="mt-4">
            <div class="divider text-xs text-white/30 uppercase tracking-widest my-3"><span>ATAU MASUKKAN KODE ABSEN</span></div>
            <form id="manualTokenForm" onsubmit="submitManualToken(event)">
                <div class="flex gap-2">
                    <input type="text" id="manual_token" name="token" placeholder="Masukkan 6 digit kode token absensi" required
                           class="flex-1 px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm focus:border-blue-500 focus:outline-none">
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white font-semibold text-sm">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Alert feedback -->
        <div id="scanFeedback" class="mt-4 p-3 rounded-lg text-xs hidden"></div>
    </div>
</div>

<!-- html5-qrcode Library for Camera Scanner -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5QrcodeScanner = null;

    function openScannerModal() {
        document.getElementById('scannerModal').style.display = 'flex';
        document.getElementById('scanFeedback').style.display = 'none';
        
        // Start QR scanning logic
        html5QrcodeScanner = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrcodeScanner.start(
            { facingMode: "environment" }, 
            config, 
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Gagal menyalakan kamera scanner: ", err);
            document.getElementById('qr-reader').innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-center p-6 text-white/50">
                    <i class="fas fa-camera-slash text-4xl mb-2 text-red-400"></i>
                    <p class="text-xs leading-normal">Kamera tidak dapat diakses atau diizinkan. Silakan gunakan opsi input kode manual di bawah.</p>
                </div>
            `;
        });
    }

    function closeScannerModal() {
        document.getElementById('scannerModal').style.display = 'none';
        if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
            html5QrcodeScanner.stop().then(() => {
                html5QrcodeScanner = null;
            }).catch(err => console.error(err));
        }
    }

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan sukses: ${decodedText}`);
        
        try {
            const url = new URL(decodedText);
            const kelasId = url.searchParams.get('kelas_id');
            const mapelId = url.searchParams.get('mapel_id');
            const jadwalPelajaranId = url.searchParams.get('jadwal_pelajaran_id');
            const tanggal = url.searchParams.get('tanggal');
            const token = url.searchParams.get('token');
            const guruId = url.searchParams.get('guru_id');
            
            if (kelasId && tanggal && token) {
                // Hentikan pemindaian agar tidak terpicu ganda
                html5QrcodeScanner.stop().then(() => {
                    postAttendanceScan(kelasId, mapelId, jadwalPelajaranId, tanggal, token, guruId);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Format QR Code salah atau tidak dikenali!',
                    background: '#0f172a',
                    color: '#ffffff'
                });
            }
        } catch (e) {
            // Jika isinya langsung token manual
            postAttendanceScanByTokenOnly(decodedText);
        }
    }

    function onScanFailure(error) {
        // Kegagalan frame biasa, bisa diabaikan agar terus melacak frame berikutnya
    }

    function postAttendanceScan(kelasId, mapelId, jadwalPelajaranId, tanggal, token, guruId) {
        Swal.fire({
            title: 'Memproses Absensi',
            text: 'Mohon tunggu sebentar...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            background: '#0f172a',
            color: '#ffffff'
        });
        
        fetch("{{ route('siswa.absensi.scan-qr') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                kelas_id: kelasId, 
                mapel_id: mapelId, 
                jadwal_pelajaran_id: jadwalPelajaranId, 
                tanggal: tanggal, 
                token: token, 
                guru_id: guruId 
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#0f172a',
                    color: '#ffffff'
                });
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Absensi',
                    text: data.message,
                    background: '#0f172a',
                    color: '#ffffff'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Absensi',
                text: 'Terjadi kesalahan koneksi atau sistem saat memproses absensi.',
                background: '#0f172a',
                color: '#ffffff'
            });
        });
    }

    function postAttendanceScanByTokenOnly(tokenStr) {
        Swal.fire({
            title: 'Memproses Kode Token',
            text: 'Mohon tunggu sebentar...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            background: '#0f172a',
            color: '#ffffff'
        });

        const todayStr = "{{ date('Y-m-d') }}";
        const studentKelasId = "{{ $siswa->kelas_id }}";
        
        fetch("{{ route('siswa.absensi.scan-qr') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ kelas_id: studentKelasId, tanggal: todayStr, token: tokenStr })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#0f172a',
                    color: '#ffffff'
                });
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Absensi',
                    text: data.message,
                    background: '#0f172a',
                    color: '#ffffff'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal memproses kode token absensi.',
                background: '#0f172a',
                color: '#ffffff'
            });
        });
    }

    function submitManualToken(event) {
        event.preventDefault();
        const tokenInput = document.getElementById('manual_token');
        postAttendanceScanByTokenOnly(tokenInput.value);
    }

    // Chart.js - Grafik Kemajuan Akademik Siswa
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('studentGradesChart');
        if (!ctx) return;

        // Ambil data nilai secara dinamis dari PHP
        @php
            $nilaiMapels = \App\Models\Penilaian::with('mapel')->where('siswa_id', $siswa->id)->get();
            $labels = [];
            $dataNilai = [];
            foreach ($nilaiMapels as $n) {
                $labels[] = $n->mapel->nama_mapel;
                $dataNilai[] = $n->nilai_akhir;
            }
        @endphp

        const labels = {!! json_encode($labels) !!};
        const dataValues = {!! json_encode($dataNilai) !!};

        if (labels.length === 0) {
            // Tampilkan visual kosong jika belum ada nilai
            ctx.parentElement.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-center p-6 text-white/30">
                    <i class="fas fa-folder-open text-4xl mb-2 text-white/10"></i>
                    <p class="text-sm">Belum ada rekap nilai ujian yang masuk saat ini.</p>
                </div>
            `;
            return;
        }

        const config = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Akhir',
                    data: dataValues,
                    backgroundColor: 'rgba(59, 130, 246, 0.4)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    hoverBackgroundColor: 'rgba(245, 158, 11, 0.5)',
                    hoverBorderColor: 'rgba(245, 158, 11, 1)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.4)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.4)',
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        };

        let chart = new Chart(ctx, config);

        // Update warna jika tema dirubah
        window.addEventListener('theme-changed', function() {
            const isLight = document.body.classList.contains('light-theme');
            chart.options.scales.y.grid.color = isLight ? 'rgba(0, 0, 0, 0.05)' : 'rgba(255, 255, 255, 0.05)';
            chart.options.scales.y.ticks.color = isLight ? 'rgba(0, 0, 0, 0.6)' : 'rgba(255, 255, 255, 0.4)';
            chart.options.scales.x.ticks.color = isLight ? 'rgba(0, 0, 0, 0.6)' : 'rgba(255, 255, 255, 0.4)';
            chart.update();
        });
    });
</script>
@endsection
