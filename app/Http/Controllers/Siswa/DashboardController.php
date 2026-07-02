<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\Penilaian;
use App\Models\AcademicCalendar;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard Utama Siswa.
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Akun Anda belum dikaitkan dengan data siswa manapun.');
        }

        $kelas = $siswa->kelas;

        // Statistik Kehadiran
        $hadir = Absensi::where('siswa_id', $siswa->id)->where('status', 'H')->count();
        $sakit = Absensi::where('siswa_id', $siswa->id)->where('status', 'S')->count();
        $izin = Absensi::where('siswa_id', $siswa->id)->where('status', 'I')->count();
        $alpa = Absensi::where('siswa_id', $siswa->id)->where('status', 'A')->count();
        $totalAbsen = $hadir + $sakit + $izin + $alpa;
        $persentaseKehadiran = $totalAbsen > 0 ? ($hadir / $totalAbsen) * 100 : 100;

        // Rata-rata Nilai Akhir
        $rataNilai = Penilaian::where('siswa_id', $siswa->id)->avg('nilai_akhir') ?? 0;

        // Tugas Aktif (belum lewat deadline)
        $tugasAktif = Tugas::where('kelas_id', $siswa->kelas_id)
                           ->where('deadline', '>', Carbon::now())
                           ->count();

        // Kalender Akademik Terbaru
        $calendarEvents = AcademicCalendar::orderBy('start_date', 'asc')->take(5)->get();

        // Pengumuman Mock (karena belum ada tabel pengumuman, kita buat dinamis)
        $announcements = [
            [
                'title' => 'Ujian Akhir Semester Genap',
                'content' => 'Ujian Akhir Semester Genap akan dilaksanakan mulai tanggal 15 Juni 2026. Persiapkan diri Anda dan lunasi administrasi sekolah.',
                'date' => '08 Jun 2026',
                'author' => 'Admin Kurikulum'
            ],
            [
                'title' => 'Pengisian Angket Ekstrakurikuler',
                'content' => 'Kepada seluruh siswa kelas X dan XI wajib mengisi formulir pemilihan ekstrakurikuler tahun ajaran baru paling lambat akhir pekan ini.',
                'date' => '05 Jun 2026',
                'author' => 'Kesiswaan'
            ]
        ];

        return view('siswa.dashboard', compact(
            'siswa', 'kelas', 'hadir', 'sakit', 'izin', 'alpa', 
            'persentaseKehadiran', 'rataNilai', 'tugasAktif', 
            'calendarEvents', 'announcements'
        ));
    }

    /**
     * Menampilkan Materi Pelajaran untuk kelas siswa.
     */
    public function materi()
    {
        $siswa = Auth::user()->siswa;
        $materi = Materi::with('mapel', 'guru')
                        ->where('kelas_id', $siswa->kelas_id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('siswa.materi', compact('materi'));
    }

    /**
     * Menampilkan Tugas untuk kelas siswa.
     */
    public function tugas()
    {
        $siswa = Auth::user()->siswa;
        $tugas = Tugas::with('mapel', 'guru')
                      ->where('kelas_id', $siswa->kelas_id)
                      ->orderBy('deadline', 'asc')
                      ->get();

        // Ambil status pengumpulan tugas siswa
        $submissions = PengumpulanTugas::where('siswa_id', $siswa->id)
                                       ->get()
                                       ->keyBy('tugas_id');

        return view('siswa.tugas', compact('tugas', 'submissions'));
    }

    /**
     * Mengirim / mengumpulkan file tugas.
     */
    public function submitTugas(Request $request, $tugasId)
    {
        $request->validate([
            'file_submit' => 'required|file|mimes:pdf,zip,rar,doc,docx,jpg,png,jpeg|max:5120',
            'catatan' => 'nullable|string|max:500'
        ], [
            'file_submit.required' => 'File tugas wajib diunggah',
            'file_submit.mimes' => 'Format file tugas tidak valid. Gunakan PDF, ZIP, RAR, Word, atau Gambar.',
            'file_submit.max' => 'Ukuran file tugas maksimal adalah 5MB'
        ]);

        $siswa = Auth::user()->siswa;
        $tugas = Tugas::findOrFail($tugasId);

        // Cek jika sudah lewat deadline
        if (Carbon::now()->greaterThan($tugas->deadline)) {
            return redirect()->back()->with('error', 'Waktu pengumpulan tugas ini sudah habis (Melewati batas waktu).');
        }

        if ($request->hasFile('file_submit')) {
            $file = $request->file('file_submit');
            $fileName = 'tugas_' . $tugasId . '_siswa_' . $siswa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('submissions', $fileName, 'public');

            // Hapus file lama jika ada update pengumpulan
            $oldSubmission = PengumpulanTugas::where('tugas_id', $tugasId)->where('siswa_id', $siswa->id)->first();
            if ($oldSubmission && !empty($oldSubmission->file_submit)) {
                Storage::disk('public')->delete($oldSubmission->file_submit);
            }

            PengumpulanTugas::updateOrCreate(
                [
                    'tugas_id' => $tugasId,
                    'siswa_id' => $siswa->id
                ],
                [
                    'file_submit' => $path,
                    'catatan' => $request->catatan,
                ]
            );

            ActivityLog::log('submit_tugas', 'Siswa mengumpulkan tugas: ' . $tugas->judul);

            return redirect()->route('siswa.tugas.index')->with('success', 'Tugas berhasil dikumpulkan!');
        }

        return redirect()->back()->with('error', 'Gagal memproses file tugas.');
    }

    /**
     * Menampilkan Nilai Siswa.
     */
    public function penilaian()
    {
        $siswa = Auth::user()->siswa;
        $nilai = Penilaian::with('mapel')
                          ->where('siswa_id', $siswa->id)
                          ->get();

        return view('siswa.penilaian', compact('nilai'));
    }

    /**
     * Menampilkan riwayat Absensi Siswa.
     */
    public function absensi()
    {
        $siswa = Auth::user()->siswa;
        $absensi = Absensi::with('guru', 'mapel')->where('siswa_id', $siswa->id)
                          ->orderBy('tanggal', 'desc')
                          ->paginate(15);

        // Cari persentase kehadiran
        $hadir = Absensi::where('siswa_id', $siswa->id)->where('status', 'H')->count();
        $total = Absensi::where('siswa_id', $siswa->id)->count();
        $persentase = $total > 0 ? ($hadir / $total) * 100 : 100;

        return view('siswa.absensi', compact('absensi', 'persentase'));
    }

    /**
     * Proses input Kehadiran via QR Code.
     */
    public function scanQr(Request $request)
    {
        try {
            $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'tanggal' => 'required|date',
                'token' => 'required|string',
                'mapel_id' => 'nullable|exists:mata_pelajaran,id',
                'jadwal_pelajaran_id' => 'nullable|exists:jadwal_pelajaran,id',
                'guru_id' => 'nullable|exists:users,id'
            ]);

            $siswa = Auth::user()->siswa;

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda belum dikaitkan dengan data siswa manapun.'
                ], 403);
            }

            // Validasi kecocokan kelas siswa
            if ($siswa->kelas_id != $request->kelas_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code atau token ini bukan untuk kelas Anda!'
                ], 403);
            }

            $mapelId = $request->mapel_id;
            $jadwalPelajaranId = $request->jadwal_pelajaran_id;

            // Jika tidak dikirim (misal manual token entry), cari jadwal pelajaran kelas siswa hari ini yang cocok
            if (!$mapelId || !$jadwalPelajaranId) {
                $schedules = \App\Models\JadwalPelajaran::where('kelas_id', $siswa->kelas_id)->get();
                $matched = false;
                foreach ($schedules as $sch) {
                    $tokenToCheck = substr(md5($siswa->kelas_id . $sch->mapel_id . $request->tanggal), 0, 6);
                    if ($request->token === $tokenToCheck) {
                        $mapelId = $sch->mapel_id;
                        $jadwalPelajaranId = $sch->id;
                        $matched = true;
                        break;
                    }
                }

                if (!$matched) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kode token absensi tidak valid atau sudah kadaluarsa!'
                    ], 400);
                }
            } else {
                // Verifikasi token keamanan (md5 dari gabungan kelas_id, mapel_id dan tanggal)
                $expectedToken = md5($request->kelas_id . $mapelId . $request->tanggal);
                if ($request->token !== $expectedToken && $request->token !== substr($expectedToken, 0, 6)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code atau token tidak valid!'
                    ], 400);
                }
            }

            // Simpan/update data absensi
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'jadwal_pelajaran_id' => $jadwalPelajaranId,
                    'tanggal' => $request->tanggal
                ],
                [
                    'kelas_id' => $request->kelas_id,
                    'mapel_id' => $mapelId,
                    'status' => 'H', // QR Code mencatat Hadir
                    'keterangan' => 'Absen QR Code Mandiri',
                    'guru_id' => $request->guru_id
                ]
            );

            ActivityLog::log('absen_qr', 'Siswa melakukan absensi mandiri via QR Code.');

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil! Anda tercatat HADIR hari ini.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data input tidak valid: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proses input Kehadiran via GET request (misal scan QR dari kamera bawaan).
     */
    public function scanQrGet(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'token' => 'required|string',
            'mapel_id' => 'nullable|exists:mata_pelajaran,id',
            'jadwal_pelajaran_id' => 'nullable|exists:jadwal_pelajaran,id',
            'guru_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Parameter QR Code tidak valid atau tidak lengkap!');
        }

        try {
            $siswa = Auth::user()->siswa;

            if (!$siswa) {
                return redirect()->route('login')->with('error', 'Akun Anda belum dikaitkan dengan data siswa manapun.');
            }

            // Validasi kecocokan kelas siswa
            if ($siswa->kelas_id != $request->kelas_id) {
                return redirect()->route('siswa.dashboard')->with('error', 'QR Code ini bukan untuk kelas Anda!');
            }

            $mapelId = $request->mapel_id;
            $jadwalPelajaranId = $request->jadwal_pelajaran_id;

            // Jika tidak dikirim (misal manual token entry), cari jadwal pelajaran kelas siswa hari ini yang cocok
            if (!$mapelId || !$jadwalPelajaranId) {
                $schedules = \App\Models\JadwalPelajaran::where('kelas_id', $siswa->kelas_id)->get();
                $matched = false;
                foreach ($schedules as $sch) {
                    $tokenToCheck = substr(md5($siswa->kelas_id . $sch->mapel_id . $request->tanggal), 0, 6);
                    if ($request->token === $tokenToCheck) {
                        $mapelId = $sch->mapel_id;
                        $jadwalPelajaranId = $sch->id;
                        $matched = true;
                        break;
                    }
                }

                if (!$matched) {
                    return redirect()->route('siswa.dashboard')->with('error', 'Kode token absensi tidak valid atau sudah kadaluarsa!');
                }
            } else {
                // Verifikasi token keamanan (md5 dari gabungan kelas_id, mapel_id dan tanggal)
                $expectedToken = md5($request->kelas_id . $mapelId . $request->tanggal);
                if ($request->token !== $expectedToken && $request->token !== substr($expectedToken, 0, 6)) {
                    return redirect()->route('siswa.dashboard')->with('error', 'QR Code atau token tidak valid!');
                }
            }

            // Simpan/update data absensi
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'jadwal_pelajaran_id' => $jadwalPelajaranId,
                    'tanggal' => $request->tanggal
                ],
                [
                    'kelas_id' => $request->kelas_id,
                    'mapel_id' => $mapelId,
                    'status' => 'H', // QR Code mencatat Hadir
                    'keterangan' => 'Absen QR Code Mandiri',
                    'guru_id' => $request->guru_id
                ]
            );

            ActivityLog::log('absen_qr', 'Siswa melakukan absensi mandiri via QR Code.');

            return redirect()->route('siswa.dashboard')->with('success', 'Absensi berhasil! Anda tercatat HADIR hari ini.');
        } catch (\Exception $e) {
            return redirect()->route('siswa.dashboard')->with('error', 'Terjadi kesalahan sistem saat melakukan absensi: ' . $e->getMessage());
        }
    }
}
