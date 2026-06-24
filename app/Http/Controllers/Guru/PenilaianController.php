<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    /**
     * Display the bulk grade entry form.
     */
    public function index(Request $request)
    {
        $mapels = MataPelajaran::where('guru_id', auth()->id())->get();
        $mapelIds = $mapels->pluck('id');
        
        $kelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        $siswa = collect();
        $grades = collect();
        
        $selectedKelasId = $request->get('kelas_id');
        $selectedMapelId = $request->get('mapel_id');

        if ($selectedKelasId && !in_array($selectedKelasId, $kelasIds->toArray())) {
            return redirect()->route('guru.penilaian.index')->with('error', 'Anda tidak memiliki wewenang untuk menilai kelas ini.');
        }

        if ($selectedMapelId && !in_array($selectedMapelId, $mapelIds->toArray())) {
            return redirect()->route('guru.penilaian.index')->with('error', 'Anda tidak memiliki wewenang untuk memberikan nilai pada mata pelajaran ini.');
        }

        if ($selectedKelasId && $selectedMapelId) {
            // Fetch students in the selected class
            $siswa = Siswa::where('kelas_id', $selectedKelasId)->orderBy('nama', 'asc')->get();
            
            // Fetch existing grades for these students on this subject
            $grades = Penilaian::where('mapel_id', $selectedMapelId)
                               ->whereIn('siswa_id', $siswa->pluck('id'))
                               ->get()
                               ->keyBy('siswa_id');
        }

        return view('guru.penilaian.index', compact('kelas', 'mapels', 'siswa', 'grades', 'selectedKelasId', 'selectedMapelId'));
    }

    /**
     * Store or update bulk student grades.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'grades' => 'required|array',
            'grades.*.nilai_harian_1' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_harian_2' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_harian_3' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_harian_4' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_harian_5' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_harian_6' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_uts' => 'nullable|numeric|min:0|max:100',
            'grades.*.nilai_uas' => 'nullable|numeric|min:0|max:100',
        ], [
            'kelas_id.required' => 'Kelas harus dipilih',
            'mapel_id.required' => 'Mata pelajaran harus dipilih',
            'grades.required' => 'Data nilai tidak ditemukan',
            'grades.*.nilai_harian_1.numeric' => 'Nilai harian 1 harus berupa angka',
            'grades.*.nilai_harian_1.min' => 'Nilai harian 1 minimal 0',
            'grades.*.nilai_harian_1.max' => 'Nilai harian 1 maksimal 100',
            'grades.*.nilai_harian_2.numeric' => 'Nilai harian 2 harus berupa angka',
            'grades.*.nilai_harian_2.min' => 'Nilai harian 2 minimal 0',
            'grades.*.nilai_harian_2.max' => 'Nilai harian 2 maksimal 100',
            'grades.*.nilai_harian_3.numeric' => 'Nilai harian 3 harus berupa angka',
            'grades.*.nilai_harian_3.min' => 'Nilai harian 3 minimal 0',
            'grades.*.nilai_harian_3.max' => 'Nilai harian 3 maksimal 100',
            'grades.*.nilai_harian_4.numeric' => 'Nilai harian 4 harus berupa angka',
            'grades.*.nilai_harian_4.min' => 'Nilai harian 4 minimal 0',
            'grades.*.nilai_harian_4.max' => 'Nilai harian 4 maksimal 100',
            'grades.*.nilai_harian_5.numeric' => 'Nilai harian 5 harus berupa angka',
            'grades.*.nilai_harian_5.min' => 'Nilai harian 5 minimal 0',
            'grades.*.nilai_harian_5.max' => 'Nilai harian 5 maksimal 100',
            'grades.*.nilai_harian_6.numeric' => 'Nilai harian 6 harus berupa angka',
            'grades.*.nilai_harian_6.min' => 'Nilai harian 6 minimal 0',
            'grades.*.nilai_harian_6.max' => 'Nilai harian 6 maksimal 100',
            'grades.*.nilai_uts.numeric' => 'Nilai UTS harus berupa angka',
            'grades.*.nilai_uts.min' => 'Nilai UTS minimal 0',
            'grades.*.nilai_uts.max' => 'Nilai UTS maksimal 100',
            'grades.*.nilai_uas.numeric' => 'Nilai UAS harus berupa angka',
            'grades.*.nilai_uas.min' => 'Nilai UAS minimal 0',
            'grades.*.nilai_uas.max' => 'Nilai UAS maksimal 100',
        ]);

        // Verifikasi bahwa mapel_id diajar oleh guru yang sedang login
        $allowedMapels = MataPelajaran::where('guru_id', auth()->id())->pluck('id')->toArray();
        if (!in_array($request->mapel_id, $allowedMapels)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk memberikan nilai pada mata pelajaran ini.');
        }

        // Verifikasi bahwa kelas_id terhubung dengan jadwal pelajaran guru yang sedang login
        $allowedKelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $allowedMapels)->pluck('kelas_id')->unique()->toArray();
        if (!in_array($request->kelas_id, $allowedKelasIds)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk menilai kelas ini.');
        }

        DB::beginTransaction();

        try {
            foreach ($request->grades as $siswaId => $gradeData) {
                $h1 = $gradeData['nilai_harian_1'];
                $h2 = $gradeData['nilai_harian_2'];
                $h3 = $gradeData['nilai_harian_3'];
                $h4 = $gradeData['nilai_harian_4'];
                $h5 = $gradeData['nilai_harian_5'];
                $h6 = $gradeData['nilai_harian_6'];
                $uts = $gradeData['nilai_uts'];
                $uas = $gradeData['nilai_uas'];

                // Jika semua field kosong, kita tidak membuat rekor baru (atau membiarkannya null)
                if ($h1 === null && $h2 === null && $h3 === null && $h4 === null && $h5 === null && $h6 === null && $uts === null && $uas === null) {
                    // Cek jika rekor ada, kita bisa menghapusnya atau membiarkannya null. Kita hapus agar database bersih
                    Penilaian::where('siswa_id', $siswaId)->where('mapel_id', $request->mapel_id)->delete();
                    continue;
                }

                $penilaian = Penilaian::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'mapel_id' => $request->mapel_id
                    ],
                    [
                        'nilai_harian_1' => $h1,
                        'nilai_harian_2' => $h2,
                        'nilai_harian_3' => $h3,
                        'nilai_harian_4' => $h4,
                        'nilai_harian_5' => $h5,
                        'nilai_harian_6' => $h6,
                        'nilai_uts' => $uts,
                        'nilai_uas' => $uas,
                    ]
                );

                // Kirim In-App Notification ke user siswa terkait
                $student = Siswa::find($siswaId);
                if ($student) {
                    $studentUser = \App\Models\User::where('siswa_id', $student->id)->first();
                    if ($studentUser) {
                        $mapel = MataPelajaran::find($request->mapel_id);
                        $mapelName = $mapel ? $mapel->nama_mapel : 'Pelajaran';
                        
                        $msgParts = [];
                        if ($penilaian->nilai_harian !== null) $msgParts[] = "Rata Harian: " . number_format($penilaian->nilai_harian, 1);
                        if ($uts !== null) $msgParts[] = "UTS: $uts";
                        if ($uas !== null) $msgParts[] = "UAS: $uas";
                        $notifMsg = implode(', ', $msgParts);

                        \App\Models\AppNotification::sendNotification(
                            $studentUser->id,
                            'Nilai Baru Diinput: ' . $mapelName,
                            'Nilai Anda telah diperbarui (' . $notifMsg . ').',
                            'nilai',
                            route('siswa.penilaian.index')
                        );
                    }
                }
            }

            DB::commit();
            return redirect()->route('guru.penilaian.index', [
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id
            ])->with('success', 'Nilai siswa berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Terjadi kesalahan sistem saat menyimpan nilai: ' . $e->getMessage());
        }
    }
}
