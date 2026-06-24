<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\MataPelajaran;
use App\Models\Absensi;
use App\Models\Penilaian;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Laporan Siswa per Kelas
     */
    public function siswa(Request $request)
    {
        $selectedKelasId = $request->get('kelas_id');
        $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        
        $siswa = collect();
        $selectedKelas = null;

        if ($selectedKelasId) {
            $selectedKelas = Kelas::findOrFail($selectedKelasId);
            $siswa = Siswa::where('kelas_id', $selectedKelasId)->orderBy('nama', 'asc')->get();
        }

        // Export PDF
        if ($request->get('export') === 'pdf' && $selectedKelasId) {
            return view('laporan.print.siswa-pdf', compact('siswa', 'selectedKelas'));
        }

        // Export Excel (CSV Stream)
        if ($request->get('export') === 'excel' && $selectedKelasId) {
            $fileName = "laporan_siswa_" . str_replace(' ', '_', $selectedKelas->nama_kelas) . "_" . date('Ymd') . ".csv";
            $headers = $this->getCsvHeaders($fileName);
            $columns = ['No', 'NIS', 'NISN', 'Nama Siswa', 'Kelas', 'Jenis Kelamin', 'No. Telp', 'Alamat'];

            $callback = function() use($siswa, $columns, $selectedKelas) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, $columns, ';');

                foreach ($siswa as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item->nis,
                        $item->nisn ?? '-',
                        $item->nama,
                        $selectedKelas->nama_kelas,
                        $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                        $item->telp ?? '-',
                        $item->alamat ?? '-'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('laporan.siswa', compact('kelas', 'siswa', 'selectedKelasId', 'selectedKelas'));
    }

    /**
     * Laporan Guru
     */
    public function guru(Request $request)
    {
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        // Export PDF
        if ($request->get('export') === 'pdf') {
            return view('laporan.print.guru-pdf', compact('gurus'));
        }

        // Export Excel (CSV Stream)
        if ($request->get('export') === 'excel') {
            $fileName = "laporan_data_guru_" . date('Ymd') . ".csv";
            $headers = $this->getCsvHeaders($fileName);
            $columns = ['No', 'NIP', 'Nama Guru', 'Email', 'Jenis Kelamin', 'No. Telp', 'Alamat', 'Mata Pelajaran'];

            $callback = function() use($gurus, $columns) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, $columns, ';');

                foreach ($gurus as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item->nip ?? '-',
                        $item->name,
                        $item->email,
                        $item->jenis_kelamin === 'L' ? 'Laki-laki' : ($item->jenis_kelamin === 'P' ? 'Perempuan' : '-'),
                        $item->telp ?? '-',
                        $item->alamat ?? '-',
                        $item->mapel ?? '-'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('laporan.guru', compact('gurus'));
    }

    /**
     * Laporan Rekap Absensi Bulanan
     */
    public function absensi(Request $request)
    {
        $selectedKelasId = $request->get('kelas_id');
        $selectedBulan = $request->get('bulan', date('Y-m'));

        if (auth()->user()->role === 'guru') {
            $mapelIds = MataPelajaran::where('guru_id', auth()->id())->pluck('id');
            $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
            $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
            
            if ($selectedKelasId && !$kelasIds->contains($selectedKelasId)) {
                abort(403, 'Anda tidak memiliki akses ke kelas ini.');
            }
        } else {
            $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        }
        
        $siswa = collect();
        $rekap = collect();
        $selectedKelas = null;

        if ($selectedKelasId && $selectedBulan) {
            $selectedKelas = Kelas::findOrFail($selectedKelasId);
            $siswa = Siswa::where('kelas_id', $selectedKelasId)->orderBy('nama', 'asc')->get();
            
            // Fetch raw monthly attendance for calculation
            $absensiData = Absensi::where('kelas_id', $selectedKelasId)
                ->where('tanggal', 'like', "$selectedBulan%")
                ->get()
                ->groupBy('siswa_id');

            foreach ($siswa as $item) {
                $studentRecords = $absensiData->get($item->id, collect());
                
                $h = $studentRecords->where('status', 'H')->count();
                $s = $studentRecords->where('status', 'S')->count();
                $i = $studentRecords->where('status', 'I')->count();
                $a = $studentRecords->where('status', 'A')->count();
                
                $totalDays = $h + $s + $i + $a;
                $pct = $totalDays > 0 ? round((($totalDays - $a) / $totalDays) * 100) : 100;

                $rekap->put($item->id, [
                    'H' => $h,
                    'S' => $s,
                    'I' => $i,
                    'A' => $a,
                    'total' => $totalDays,
                    'percentage' => $pct
                ]);
            }
        }

        $formattedBulan = Carbon::parse($selectedBulan . '-01')->format('F Y');

        // Export PDF
        if ($request->get('export') === 'pdf' && $selectedKelasId) {
            return view('laporan.print.absensi-pdf', compact('siswa', 'rekap', 'selectedKelas', 'selectedBulan', 'formattedBulan'));
        }

        // Export Excel (CSV Stream)
        if ($request->get('export') === 'excel' && $selectedKelasId) {
            $fileName = "rekap_absen_" . str_replace(' ', '_', $selectedKelas->nama_kelas) . "_" . $selectedBulan . ".csv";
            $headers = $this->getCsvHeaders($fileName);
            $columns = ['No', 'NIS', 'Nama Siswa', 'Hadir (H)', 'Sakit (S)', 'Izin (I)', 'Alpa (A)', 'Total Hari', 'Persentase Kehadiran'];

            $callback = function() use($siswa, $rekap, $columns) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, $columns, ';');

                foreach ($siswa as $index => $item) {
                    $itemRekap = $rekap->get($item->id);
                    fputcsv($file, [
                        $index + 1,
                        $item->nis,
                        $item->nama,
                        $itemRekap['H'],
                        $itemRekap['S'],
                        $itemRekap['I'],
                        $itemRekap['A'],
                        $itemRekap['total'],
                        $itemRekap['percentage'] . '%'
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('laporan.absensi', compact('kelas', 'siswa', 'rekap', 'selectedKelasId', 'selectedBulan', 'formattedBulan', 'selectedKelas'));
    }

    /**
     * Laporan Rekap Nilai Pelajaran
     */
    public function nilai(Request $request)
    {
        $selectedKelasId = $request->get('kelas_id');
        $selectedMapelId = $request->get('mapel_id');

        if (auth()->user()->role === 'guru') {
            $mapels = MataPelajaran::where('guru_id', auth()->id())->get();
            $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapels->pluck('id'))->pluck('kelas_id')->unique();
            $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
            
            if ($selectedKelasId && !$kelasIds->contains($selectedKelasId)) {
                abort(403, 'Anda tidak memiliki akses ke kelas ini.');
            }
            if ($selectedMapelId && $selectedMapelId !== 'all' && !$mapels->pluck('id')->contains($selectedMapelId)) {
                abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini.');
            }
        } else {
            $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
            $mapels = MataPelajaran::all();
        }

        $siswa = collect();
        $grades = collect();
        $selectedKelas = null;
        $selectedMapel = null;
        $allMapels = collect();
        $averages = [];
        $classRanks = [];
        
        $utsRanks = [];
        $uasRanks = [];
        $finalRanks = [];

        if ($selectedKelasId && $selectedMapelId) {
            $selectedKelas = Kelas::findOrFail($selectedKelasId);
            $siswa = Siswa::where('kelas_id', $selectedKelasId)->orderBy('nama', 'asc')->get();

            if ($selectedMapelId === 'all') {
                $allMapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();
                $grades = Penilaian::whereIn('siswa_id', $siswa->pluck('id'))
                    ->get()
                    ->groupBy('siswa_id');

                // Hitung Rata-rata dan Peringkat Kelas
                foreach ($siswa as $item) {
                    $studentGrades = $grades->get($item->id, collect());
                    if ($studentGrades->count() > 0) {
                        $sum = 0;
                        $count = 0;
                        foreach ($allMapels as $m) {
                            $g = $studentGrades->firstWhere('mapel_id', $m->id);
                            if ($g && $g->nilai_akhir !== null) {
                                $sum += $g->nilai_akhir;
                                $count++;
                            }
                        }
                        $averages[$item->id] = $count > 0 ? ($sum / $count) : 0;
                    } else {
                        $averages[$item->id] = 0;
                    }
                }

                // Hitung ranking
                arsort($averages);
                $rank = 1; $prevVal = null; $count = 0;
                foreach ($averages as $sid => $val) {
                    $count++;
                    if ($prevVal !== null && $val < $prevVal) {
                        $rank = $count;
                    }
                    $classRanks[$sid] = $val > 0 ? $rank : '-';
                    $prevVal = $val;
                }
            } else {
                $selectedMapel = MataPelajaran::with('guru')->findOrFail($selectedMapelId);
                $grades = Penilaian::where('mapel_id', $selectedMapelId)
                    ->whereIn('siswa_id', $siswa->pluck('id'))
                    ->get()
                    ->keyBy('siswa_id');

                // Hitung ranking UTS, UAS, dan Akhir
                $utsGrades = [];
                $uasGrades = [];
                $finalGrades = [];
                foreach ($siswa as $item) {
                    $grade = $grades->get($item->id);
                    $utsGrades[$item->id] = $grade ? $grade->nilai_uts : null;
                    $uasGrades[$item->id] = $grade ? $grade->nilai_uas : null;
                    $finalGrades[$item->id] = $grade ? $grade->nilai_akhir : null;
                }

                // Rank UTS
                arsort($utsGrades);
                $rank = 1; $prevVal = null; $count = 0;
                foreach ($utsGrades as $sid => $val) {
                    $count++;
                    if ($val === null) { $utsRanks[$sid] = '-'; continue; }
                    if ($prevVal !== null && $val < $prevVal) { $rank = $count; }
                    $utsRanks[$sid] = $rank;
                    $prevVal = $val;
                }

                // Rank UAS
                arsort($uasGrades);
                $rank = 1; $prevVal = null; $count = 0;
                foreach ($uasGrades as $sid => $val) {
                    $count++;
                    if ($val === null) { $uasRanks[$sid] = '-'; continue; }
                    if ($prevVal !== null && $val < $prevVal) { $rank = $count; }
                    $uasRanks[$sid] = $rank;
                    $prevVal = $val;
                }

                // Rank Final
                arsort($finalGrades);
                $rank = 1; $prevVal = null; $count = 0;
                foreach ($finalGrades as $sid => $val) {
                    $count++;
                    if ($val === null) { $finalRanks[$sid] = '-'; continue; }
                    if ($prevVal !== null && $val < $prevVal) { $rank = $count; }
                    $finalRanks[$sid] = $rank;
                    $prevVal = $val;
                }
            }
        }

        // Export PDF
        if ($request->get('export') === 'pdf' && $selectedKelasId && $selectedMapelId) {
            if ($selectedMapelId === 'all') {
                return view('laporan.print.nilai-all-pdf', compact('siswa', 'grades', 'selectedKelas', 'allMapels', 'averages', 'classRanks'));
            } else {
                return view('laporan.print.nilai-pdf', compact('siswa', 'grades', 'selectedKelas', 'selectedMapel', 'utsRanks', 'uasRanks', 'finalRanks'));
            }
        }

        // Export Excel (CSV Stream)
        if ($request->get('export') === 'excel' && $selectedKelasId && $selectedMapelId) {
            if ($selectedMapelId === 'all') {
                $fileName = "rekap_nilai_semua_mapel_" . str_replace(' ', '_', $selectedKelas->nama_kelas) . ".csv";
                $headers = $this->getCsvHeaders($fileName);
                
                // Columns
                $columns = ['No', 'NIS', 'Nama Siswa'];
                foreach ($allMapels as $m) {
                    $columns[] = $m->nama_mapel;
                }
                $columns[] = 'Rata-rata';
                $columns[] = 'Peringkat Kelas';

                $callback = function() use($siswa, $grades, $allMapels, $averages, $classRanks, $columns) {
                    $file = fopen('php://output', 'w');
                    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                    fputcsv($file, $columns, ';');

                    foreach ($siswa as $index => $item) {
                        $studentGrades = $grades->get($item->id, collect());
                        
                        $row = [
                            $index + 1,
                            $item->nis,
                            $item->nama,
                        ];

                        foreach ($allMapels as $m) {
                            $g = $studentGrades->firstWhere('mapel_id', $m->id);
                            $row[] = $g ? ($g->nilai_akhir ?? '-') : '-';
                        }

                        $row[] = isset($averages[$item->id]) ? round($averages[$item->id], 1) : '-';
                        $row[] = $classRanks[$item->id] ?? '-';

                        fputcsv($file, $row, ';');
                    }
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            } else {
                $fileName = "rekap_nilai_" . str_replace(' ', '_', $selectedKelas->nama_kelas) . "_" . str_replace(' ', '_', $selectedMapel->nama_mapel) . ".csv";
                $headers = $this->getCsvHeaders($fileName);
                $columns = ['No', 'NIS', 'Nama Siswa', 'Rata Harian (40%)', 'Nilai UTS (30%)', 'Peringkat UTS', 'Nilai UAS (30%)', 'Peringkat UAS', 'Nilai Akhir', 'Peringkat Akhir'];

                $callback = function() use($siswa, $grades, $utsRanks, $uasRanks, $finalRanks, $columns) {
                    $file = fopen('php://output', 'w');
                    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                    fputcsv($file, $columns, ';');

                    foreach ($siswa as $index => $item) {
                        $grade = $grades->get($item->id);
                        $harian = $grade ? $grade->nilai_harian : null;
                        $uts = $grade ? $grade->nilai_uts : null;
                        $uas = $grade ? $grade->nilai_uas : null;
                        $nilaiAkhir = $grade ? $grade->nilai_akhir : null;

                        fputcsv($file, [
                            $index + 1,
                            $item->nis,
                            $item->nama,
                            $harian !== null ? round($harian, 1) : '-',
                            $uts ?? '-',
                            $utsRanks[$item->id] ?? '-',
                            $uas ?? '-',
                            $uasRanks[$item->id] ?? '-',
                            $nilaiAkhir !== null ? round($nilaiAkhir) : '-',
                            $finalRanks[$item->id] ?? '-'
                        ], ';');
                    }
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }
        }

        return view('laporan.nilai', compact(
            'kelas', 'mapels', 'siswa', 'grades', 'selectedKelasId', 'selectedMapelId', 
            'selectedKelas', 'selectedMapel', 'allMapels', 'averages', 'classRanks', 
            'utsRanks', 'uasRanks', 'finalRanks'
        ));
    }

    /**
     * CSV Headers helper
     */
    private function getCsvHeaders($fileName)
    {
        return [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=" . $fileName,
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
    }
}
