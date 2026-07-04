<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\Guru\TugasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\Guru\MateriController as GuruMateriController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\Guru\PenilaianController as GuruPenilaianController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\Guru\KelasSiswaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Guru\UjianController as GuruUjianController;
use App\Http\Controllers\Siswa\UjianController as SiswaUjianController;



Route::get('/', function () {
   return redirect()->route('login');
});

Route::get('/test-404', function () {
    abort(404);
});

Route::get('/login', [AuthController::class, 'loginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


    Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('/kelas', KelasController::class);
        Route::resource('/guru', GuruController::class);
        Route::get('/siswa/download-template', [SiswaController::class, 'downloadTemplate'])->name('siswa.download-template');
        Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
        Route::post('/siswa/kenaikan-kelas', [SiswaController::class, 'kenaikanKelas'])->name('siswa.kenaikan-kelas');
        Route::resource('/siswa', SiswaController::class);
        Route::resource('/mata-pelajaran', MataPelajaranController::class);
        Route::resource('/materi', MateriController::class);
        Route::resource('/penilaian', PenilaianController::class);
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/export', [AbsensiController::class, 'export'])->name('absensi.export');
        Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');

        // Fitur Tambahan Admin
        Route::get('/backup', [BackupController::class, 'backup'])->name('backup');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::resource('/calendar', AcademicCalendarController::class);
        Route::resource('/tahun-akademik', TahunAkademikController::class);
        Route::resource('/pengumuman', PengumumanController::class);
        Route::resource('/jadwal', JadwalPelajaranController::class);
        Route::get('/ujian/monitoring', [\App\Http\Controllers\Admin\UjianMonitoringController::class, 'index'])->name('ujian.monitoring');
        
        // Rute Laporan Admin
        Route::get('/laporan/siswa', [LaporanController::class, 'siswa'])->name('laporan.siswa');
        Route::get('/laporan/guru', [LaporanController::class, 'guru'])->name('laporan.guru');
        Route::get('/laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
        Route::get('/laporan/nilai', [LaporanController::class, 'nilai'])->name('laporan.nilai');

        // Rute Jurnal Mengajar Admin
        Route::get('/jurnal/rekap', [\App\Http\Controllers\Admin\JurnalMengajarController::class, 'rekap'])->name('jurnal.rekap');
        Route::resource('/jurnal', \App\Http\Controllers\Admin\JurnalMengajarController::class)->only(['index', 'show', 'destroy']);
    });

Route::middleware(['auth', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('guru.dashboard');
        })->name('dashboard');

        Route::middleware(['has_subject'])->group(function () {
            Route::resource('/tugas', TugasController::class);
            Route::post('/tugas/nilai/{submissionId}', [TugasController::class, 'gradeSubmission'])->name('tugas.grade');
            Route::resource('/materi', GuruMateriController::class);
            Route::get('/penilaian', [GuruPenilaianController::class, 'index'])->name('penilaian.index');
            Route::post('/penilaian/store', [GuruPenilaianController::class, 'store'])->name('penilaian.store');
            Route::get('/absensi', [GuruAbsensiController::class, 'index'])->name('absensi.index');
            Route::post('/absensi/store', [GuruAbsensiController::class, 'store'])->name('absensi.store');
            Route::get('/absensi/export', [GuruAbsensiController::class, 'export'])->name('absensi.export');
            Route::resource('/pengumuman', PengumumanController::class);
            Route::get('/jadwal', [JadwalPelajaranController::class, 'guruIndex'])->name('jadwal.index');
            Route::get('/kelas', [KelasSiswaController::class, 'kelasIndex'])->name('kelas.index');
            Route::get('/kelas/{id}/siswa', [KelasSiswaController::class, 'siswaIndex'])->name('kelas.siswa');
            
            // Rute Laporan Guru
            Route::get('/laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
            Route::get('/laporan/nilai', [LaporanController::class, 'nilai'])->name('laporan.nilai');

            // Rute Jurnal Mengajar Guru
            Route::get('/jurnal/rekap', [\App\Http\Controllers\Guru\JurnalMengajarController::class, 'rekap'])->name('jurnal.rekap');
            Route::resource('/jurnal', \App\Http\Controllers\Guru\JurnalMengajarController::class);

            // Rute Ujian Guru
            Route::resource('/ujian', GuruUjianController::class);
            Route::get('/ujian/{ujian}/soal', [GuruUjianController::class, 'soal'])->name('ujian.soal');
            Route::post('/ujian/{ujian}/soal', [GuruUjianController::class, 'storeSoal'])->name('ujian.soal.store');
            Route::put('/ujian/soal/{soal}', [GuruUjianController::class, 'updateSoal'])->name('ujian.soal.update');
            Route::delete('/ujian/soal/{soal}', [GuruUjianController::class, 'destroySoal'])->name('ujian.soal.destroy');
            Route::get('/ujian/{ujian}/hasil', [GuruUjianController::class, 'hasil'])->name('ujian.hasil');
            Route::get('/ujian/{ujian}/export-pdf', [GuruUjianController::class, 'exportPdf'])->name('ujian.export-pdf');
            Route::get('/ujian/{ujian}/export-word', [GuruUjianController::class, 'exportWord'])->name('ujian.export-word');
        });
    });

// Rute bersama untuk semua user terautentikasi
Route::middleware(['auth'])->group(function() {
    Route::get('/calendar-events', [AcademicCalendarController::class, 'getEventsJson'])->name('calendar.events');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::redirect('/pengaturan', '/profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/announcements/{id}', [PengumumanController::class, 'showDetail'])->name('announcements.show-detail');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    
    // Rute Chatbot AI
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::post('/chatbot/clear', [ChatbotController::class, 'clear'])->name('chatbot.clear');

    // Rute Jadwal Sholat API
    Route::get('/api/jadwal-sholat', [App\Http\Controllers\PrayerScheduleController::class, 'getSchedule']);
});

// Rute khusus untuk Siswa
Route::middleware(['auth', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
        Route::get('/materi', [SiswaDashboardController::class, 'materi'])->name('materi.index');
        Route::get('/tugas', [SiswaDashboardController::class, 'tugas'])->name('tugas.index');
        Route::post('/tugas/submit/{tugas}', [SiswaDashboardController::class, 'submitTugas'])->name('tugas.submit');
        Route::get('/penilaian', [SiswaDashboardController::class, 'penilaian'])->name('penilaian.index');
        Route::get('/absensi', [SiswaDashboardController::class, 'absensi'])->name('absensi.index');
        Route::post('/absensi/scan-qr', [SiswaDashboardController::class, 'scanQr'])->name('absensi.scan-qr');
        Route::get('/absen-qr-code', [SiswaDashboardController::class, 'scanQrGet'])->name('absensi.scan-qr-get');
        Route::get('/pengumuman', [PengumumanController::class, 'siswaIndex'])->name('pengumuman.index');
        Route::get('/jadwal', [JadwalPelajaranController::class, 'siswaIndex'])->name('jadwal.index');

        // Rute Ujian Siswa (Hanya bisa diakses via Exambro)
        Route::middleware(['exambro'])->group(function () {
            Route::get('/ujian', [SiswaUjianController::class, 'index'])->name('ujian.index');
            Route::get('/ujian/{ujian}', [SiswaUjianController::class, 'show'])->name('ujian.show');
            Route::post('/ujian/{ujian}/mulai', [SiswaUjianController::class, 'mulai'])->name('ujian.mulai');
            Route::get('/ujian/{ujian}/kerjakan', [SiswaUjianController::class, 'kerjakan'])->name('ujian.kerjakan');
            Route::post('/ujian/{ujian}/simpan-jawaban', [SiswaUjianController::class, 'simpanJawaban'])->name('ujian.simpan-jawaban');
            Route::post('/ujian/{ujian}/selesai', [SiswaUjianController::class, 'selesai'])->name('ujian.selesai');
            Route::get('/ujian/{ujian}/hasil', [SiswaUjianController::class, 'hasil'])->name('ujian.hasil');
        });
    });



    Route::get('/gemini-test', function () {
    $response = Http::post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key='.env('GEMINI_API_KEY'),
        [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'Halo']
                    ]
                ]
            ]
        ]
    );

    return $response->body();
});

Route::get('/debug-headers', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'user_agent_from_method' => $request->userAgent(),
        'user_agent_from_header' => $request->header('User-Agent'),
        'user_agent_from_server' => $request->server('HTTP_USER_AGENT'),
        'x_requested_with' => $request->header('X-Requested-With'),
        'all_headers' => $request->headers->all(),
        'server_variables' => array_filter($_SERVER, function($key) {
            return str_starts_with($key, 'HTTP_') || in_array($key, ['REQUEST_URI', 'REQUEST_METHOD', 'REMOTE_ADDR']);
        }, ARRAY_FILTER_USE_KEY)
    ]);
});