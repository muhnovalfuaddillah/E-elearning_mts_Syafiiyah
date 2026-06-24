<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Display the daily bulk attendance entry form.
     */
    public function index(Request $request)
    {
        $mapelIds = \App\Models\MataPelajaran::where('guru_id', auth()->id())->pluck('id');
        $jadwals = \App\Models\JadwalPelajaran::whereIn('mapel_id', $mapelIds)->with('kelas', 'mapel')->get();
        
        $selectedJadwalId = $request->get('jadwal_pelajaran_id');
        $selectedTanggal = $request->get('tanggal', date('Y-m-d'));

        $selectedKelasId = null;
        $selectedMapelId = null;
        $siswa = collect();
        $records = collect();

        if ($selectedJadwalId) {
            $jadwal = \App\Models\JadwalPelajaran::with('kelas', 'mapel')->findOrFail($selectedJadwalId);

            // Validasi kepemilikan jadwal
            if (!in_array($jadwal->mapel_id, $mapelIds->toArray())) {
                return redirect()->route('guru.absensi.index')->with('error', 'Anda tidak memiliki wewenang untuk melihat absensi jadwal ini.');
            }

            $selectedKelasId = $jadwal->kelas_id;
            $selectedMapelId = $jadwal->mapel_id;

            $siswa = Siswa::where('kelas_id', $selectedKelasId)->orderBy('nama', 'asc')->get();
            $records = Absensi::with('guru')->where('kelas_id', $selectedKelasId)
                              ->where('mapel_id', $selectedMapelId)
                              ->where('jadwal_pelajaran_id', $selectedJadwalId)
                              ->where('tanggal', $selectedTanggal)
                              ->get()
                              ->keyBy('siswa_id');
        }

        return view('guru.absensi.index', compact('jadwals', 'siswa', 'records', 'selectedJadwalId', 'selectedKelasId', 'selectedMapelId', 'selectedTanggal'));
    }

    /**
     * Store or update bulk student attendance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date|before_or_equal:today',
            'absensi' => 'required|array',
            'absensi.*.status' => 'required|in:H,S,I,A',
            'absensi.*.keterangan' => 'nullable|string|max:255',
        ], [
            'jadwal_pelajaran_id.required' => 'Jadwal pelajaran harus dipilih',
            'tanggal.required' => 'Tanggal harus dipilih',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan',
            'absensi.required' => 'Data absensi tidak ditemukan',
            'absensi.*.status.required' => 'Status kehadiran wajib dipilih',
            'absensi.*.status.in' => 'Status kehadiran tidak valid',
            'absensi.*.keterangan.max' => 'Keterangan maksimal 255 karakter',
        ]);

        $jadwal = \App\Models\JadwalPelajaran::findOrFail($request->jadwal_pelajaran_id);
        
        $mapelIds = \App\Models\MataPelajaran::where('guru_id', auth()->id())->pluck('id');
        if (!in_array($jadwal->mapel_id, $mapelIds->toArray())) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki wewenang untuk mengisi absensi jadwal ini.');
        }

        DB::beginTransaction();

        try {
            foreach ($request->absensi as $siswaId => $data) {
                Absensi::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
                        'tanggal' => $request->tanggal,
                    ],
                    [
                        'kelas_id' => $jadwal->kelas_id,
                        'mapel_id' => $jadwal->mapel_id,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'guru_id' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            // Kirim notifikasi WA & In-App untuk siswa yang Alpa
            foreach ($request->absensi as $siswaId => $data) {
                if ($data['status'] === 'A') {
                    $siswa = Siswa::find($siswaId);
                    
                    // In-App Notification
                    $studentUser = \App\Models\User::where('siswa_id', $siswaId)->first();
                    if ($studentUser) {
                        $tanggalFormatted = \Carbon\Carbon::parse($request->tanggal)->format('d M Y');
                        \App\Models\AppNotification::sendNotification(
                            $studentUser->id,
                            'Pemberitahuan Absensi: ALPA',
                            "Anda tercatat ALPA pada tanggal {$tanggalFormatted}.",
                            'absensi',
                            route('siswa.absensi.index')
                        );
                    }

                    // WhatsApp Notification
                    if ($siswa && $siswa->telp) {
                        $tanggalFormatted = \Carbon\Carbon::parse($request->tanggal)->format('d M Y');
                        $msg = "Pemberitahuan Absensi MTs Syafiiyah:\n"
                             . "Siswa *{$siswa->nama}* (NIS: {$siswa->nis}) tercatat *ALPA* (Tidak Hadir tanpa keterangan) pada tanggal {$tanggalFormatted}.\n\n"
                             . "Mohon segera lakukan konfirmasi ke wali kelas atau sekolah. Terima kasih.";
                        
                        \App\Services\FonnteService::send($siswa->telp, $msg);
                    }
                }
            }

            return redirect()->route('guru.absensi.index', [
                'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
                'tanggal' => $request->tanggal
            ])->with('success', 'Absensi siswa berhasil disimpan dan notifikasi WhatsApp Alpa telah dikirim!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Terjadi kesalahan sistem saat menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Export daily attendance list to CSV.
     */
    public function export(Request $request)
    {
        $request->validate([
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
        ]);

        $jadwal = \App\Models\JadwalPelajaran::with('kelas', 'mapel')->findOrFail($request->jadwal_pelajaran_id);
        
        $mapelIds = \App\Models\MataPelajaran::where('guru_id', auth()->id())->pluck('id');
        if (!in_array($jadwal->mapel_id, $mapelIds->toArray())) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk mengekspor absensi jadwal ini.');
        }

        $kelas = $jadwal->kelas;
        $mapel = $jadwal->mapel;
        $siswa = Siswa::where('kelas_id', $kelas->id)->orderBy('nama', 'asc')->get();
        
        $absensi = Absensi::with('guru')->where('kelas_id', $kelas->id)
                          ->where('jadwal_pelajaran_id', $jadwal->id)
                          ->where('tanggal', $request->tanggal)
                          ->get()
                          ->keyBy('siswa_id');

        $fileName = "absensi_" . str_replace(' ', '_', $kelas->kode_kelas) . "_" . str_replace(' ', '_', $mapel->nama_mapel) . "_" . $request->tanggal . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $fileName,
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Jam Mengajar', 'Tanggal', 'Jam Absen', 'Status Kehadiran', 'Guru Pencatat', 'Keterangan'];

        $callback = function() use($siswa, $absensi, $columns, $request, $kelas, $mapel, $jadwal) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            $statusMap = [
                'H' => 'Hadir',
                'S' => 'Sakit',
                'I' => 'Izin',
                'A' => 'Alpa'
            ];

            foreach ($siswa as $index => $item) {
                $record = $absensi->get($item->id);
                
                $status = $record ? ($statusMap[$record->status] ?? '-') : 'Belum Absen';
                $jam = $record && $record->created_at ? $record->created_at->format('H:i') : '-';
                $guruPencatat = $record && $record->guru ? $record->guru->name : ($record ? 'Sistem (QR Code)' : '-');
                $keterangan = $record ? ($record->keterangan ?? '-') : '-';

                fputcsv($file, [
                    $index + 1,
                    $item->nis,
                    $item->nama,
                    $kelas->kode_kelas,
                    $mapel->nama_mapel,
                    $jadwal->hari . ' (' . $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai . ')',
                    $request->tanggal,
                    $jam,
                    $status,
                    $guruPencatat,
                    $keterangan
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
