<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\UjianSoal;
use App\Models\UjianSiswa;
use App\Models\UjianJawaban;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UjianController extends Controller
{
    /**
     * Tampilkan daftar ujian untuk siswa.
     */
    public function index()
    {
        $siswa = Auth::user()->siswa;
        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Akun Anda belum dikaitkan dengan data siswa.');
        }

        // Ambil semua ujian yang ditujukan untuk kelas siswa tersebut
        // dan berstatus published atau closed
        $ujians = Ujian::with('mapel', 'guru')
            ->where('kelas_id', $siswa->kelas_id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        // Ambil riwayat pengerjaan ujian siswa
        $ujianSiswas = UjianSiswa::where('siswa_id', $siswa->id)
            ->get()
            ->keyBy('ujian_id');

        return view('siswa.ujian.index', compact('ujians', 'ujianSiswas'));
    }

    /**
     * Tampilkan halaman detail / konfirmasi pengerjaan ujian.
     */
    public function show($id)
    {
        if (env('ENFORCE_EXAMBROWSER', true)) {
            $userAgent = request()->header('User-Agent');
            $userAgentLower = strtolower($userAgent);
            $isExamBrowser = str_contains($userAgentLower, 'exambrowser') || 
                             str_contains($userAgentLower, 'exambro') || 
                             str_contains($userAgentLower, 'seb') ||
                             str_contains($userAgentLower, 'safeexambrowser') ||
                             str_contains($userAgentLower, '; wv') || 
                             str_contains($userAgentLower, ' wv') ||
                             str_contains($userAgentLower, 'mtssyafiiyah') ||
                             str_contains($userAgentLower, 'androidmobile');
            if (!$isExamBrowser) {
                return response()->view('siswa.ujian.download-exambro', [
                    'downloadUrl' => 'https://play.google.com/store/apps/details?id=com.exambrowser.app',
                    'userAgent' => $userAgent
                ]);
            }
        }

        $siswa = Auth::user()->siswa;
        $ujian = Ujian::with('mapel', 'guru')->findOrFail($id);

        // Pastikan ujian untuk kelas siswa
        if ($ujian->kelas_id != $siswa->kelas_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk ujian ini.');
        }

        // Cek riwayat
        $sesi = UjianSiswa::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        // Jumlah soal
        $jumlahSoal = $ujian->soals()->count();

        return view('siswa.ujian.show', compact('ujian', 'sesi', 'jumlahSoal'));
    }

    /**
     * Mulai ujian (buat sesi pengerjaan).
     */
    public function mulai($id)
    {
        $siswa = Auth::user()->siswa;
        $ujian = Ujian::findOrFail($id);

        // Pastikan ujian untuk kelas siswa
        if ($ujian->kelas_id != $siswa->kelas_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk ujian ini.');
        }

        // Cek apakah sudah ditutup atau draf
        if ($ujian->status !== 'published') {
            return redirect()->back()->with('error', 'Ujian ini tidak sedang aktif.');
        }

        $now = Carbon::now();
        // Cek rentang waktu pengerjaan
        if ($now->lessThan($ujian->waktu_mulai)) {
            return redirect()->back()->with('error', 'Ujian belum dimulai. Silakan tunggu hingga waktu mulai.');
        }
        if ($now->greaterThan($ujian->waktu_selesai)) {
            return redirect()->back()->with('error', 'Waktu pengerjaan ujian sudah berakhir.');
        }

        // Cek apakah sudah pernah membuat sesi
        $sesi = UjianSiswa::firstOrCreate(
            [
                'ujian_id' => $ujian->id,
                'siswa_id' => $siswa->id,
            ],
            [
                'waktu_mulai' => $now,
                'status' => 'mengerjakan',
            ]
        );

        if ($sesi->status === 'selesai') {
            return redirect()->route('siswa.ujian.hasil', $ujian->id)->with('error', 'Anda telah menyelesaikan ujian ini.');
        }

        ActivityLog::log('start_ujian', 'Siswa memulai pengerjaan ujian: ' . $ujian->judul);

        return redirect()->route('siswa.ujian.kerjakan', $ujian->id);
    }

    /**
     * Lembar kerja ujian.
     */
    public function kerjakan($id)
    {
        if (env('ENFORCE_EXAMBROWSER', true)) {
            $userAgent = request()->header('User-Agent');
            $userAgentLower = strtolower($userAgent);
            $isExamBrowser = str_contains($userAgentLower, 'exambrowser') || 
                             str_contains($userAgentLower, 'exambro') || 
                             str_contains($userAgentLower, 'seb') ||
                             str_contains($userAgentLower, 'safeexambrowser') ||
                             str_contains($userAgentLower, '; wv') || 
                             str_contains($userAgentLower, ' wv') ||
                             str_contains($userAgentLower, 'mtssyafiiyah') ||
                             str_contains($userAgentLower, 'androidmobile');
            if (!$isExamBrowser) {
                return response()->view('siswa.ujian.download-exambro', [
                    'downloadUrl' => 'https://play.google.com/store/apps/details?id=com.exambrowser.app',
                    'userAgent' => $userAgent
                ]);
            }
        }

        $siswa = Auth::user()->siswa;
        $ujian = Ujian::with(['soals'])->findOrFail($id);

        if ($ujian->kelas_id != $siswa->kelas_id) {
            abort(403);
        }

        $sesi = UjianSiswa::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        if (!$sesi || $sesi->status === 'selesai') {
            return redirect()->route('siswa.ujian.show', $ujian->id)->with('error', 'Sesi pengerjaan tidak aktif.');
        }

        // Hitung sisa waktu berdasarkan waktu_mulai + durasi menit
        $waktuMulai = Carbon::parse($sesi->waktu_mulai);
        $waktuHabis = $waktuMulai->copy()->addMinutes($ujian->durasi);
        
        // Batasi juga dengan waktu_selesai ujian (mana yang lebih cepat)
        $waktuSelesaiUjian = Carbon::parse($ujian->waktu_selesai);
        if ($waktuHabis->greaterThan($waktuSelesaiUjian)) {
            $waktuHabis = $waktuSelesaiUjian;
        }

        $sisaDetik = Carbon::now()->diffInSeconds($waktuHabis, false);

        if ($sisaDetik <= 0) {
            return $this->prosesSelesaiUjian($sesi, $ujian);
        }

        // Dapatkan semua jawaban yang telah disimpan oleh siswa
        $jawabans = UjianJawaban::where('ujian_siswa_id', $sesi->id)
            ->get()
            ->keyBy('ujian_soal_id');

        return view('siswa.ujian.kerjakan', compact('ujian', 'sesi', 'sisaDetik', 'jawabans'));
    }

    /**
     * Simpan jawaban sementara via AJAX.
     */
    public function simpanJawaban(Request $request, $id)
    {
        $siswa = Auth::user()->siswa;
        $ujian = Ujian::findOrFail($id);

        $sesi = UjianSiswa::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'mengerjakan')
            ->first();

        if (!$sesi) {
            return response()->json(['success' => false, 'message' => 'Sesi tidak aktif'], 403);
        }

        // Validasi input
        $request->validate([
            'soal_id' => 'required|exists:ujian_soals,id',
            'jawaban' => 'nullable|in:A,B,C,D,E',
        ]);

        $soal = UjianSoal::findOrFail($request->soal_id);

        // Cek kebenaran jawaban
        $isBenar = ($request->jawaban === $soal->kunci_jawaban);

        UjianJawaban::updateOrCreate(
            [
                'ujian_siswa_id' => $sesi->id,
                'ujian_soal_id' => $soal->id,
            ],
            [
                'jawaban' => $request->jawaban,
                'is_benar' => $isBenar,
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Kirim lembar ujian (selesai secara manual).
     */
    public function selesai($id)
    {
        $siswa = Auth::user()->siswa;
        $ujian = Ujian::findOrFail($id);

        $sesi = UjianSiswa::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'mengerjakan')
            ->first();

        if (!$sesi) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Sesi pengerjaan tidak ditemukan.');
        }

        return $this->prosesSelesaiUjian($sesi, $ujian);
    }

    /**
     * Tampilkan hasil pengerjaan ujian.
     */
    public function hasil($id)
    {
        $siswa = Auth::user()->siswa;
        $ujian = Ujian::with('mapel', 'guru')->findOrFail($id);

        $sesi = UjianSiswa::where('ujian_id', $ujian->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', 'selesai')
            ->first();

        if (!$sesi) {
            return redirect()->route('siswa.ujian.show', $ujian->id)->with('error', 'Anda belum menyelesaikan ujian ini.');
        }

        // Hitung statistik singkat
        $totalSoal = $ujian->soals()->count();
        $totalBenar = UjianJawaban::where('ujian_siswa_id', $sesi->id)->where('is_benar', true)->count();
        $totalSalah = $totalSoal - $totalBenar;

        return view('siswa.ujian.hasil', compact('ujian', 'sesi', 'totalSoal', 'totalBenar', 'totalSalah'));
    }

    /**
     * Metode internal untuk memproses akhir sesi ujian dan menghitung nilai.
     */
    private function prosesSelesaiUjian(UjianSiswa $sesi, Ujian $ujian)
    {
        $totalSoal = $ujian->soals()->count();
        
        if ($totalSoal > 0) {
            $jawabanBenar = UjianJawaban::where('ujian_siswa_id', $sesi->id)
                ->where('is_benar', true)
                ->count();
            
            // Hitung nilai skala 100
            $nilai = ($jawabanBenar / $totalSoal) * 100;
        } else {
            $nilai = 0;
        }

        $sesi->update([
            'nilai' => $nilai,
            'waktu_selesai' => Carbon::now(),
            'status' => 'selesai',
        ]);

        // Secara otomatis masukkan ke tabel penilaian umum jika diperlukan,
        // namun untuk saat ini, nilai disimpan independen di tabel ujian_siswas.
        
        ActivityLog::log('finish_ujian', 'Siswa menyelesaikan ujian: ' . $ujian->judul . ' dengan nilai ' . round($nilai, 2));

        return redirect()->route('siswa.ujian.hasil', $ujian->id)->with('success', 'Ujian selesai! Nilai Anda berhasil disimpan.');
    }
}
