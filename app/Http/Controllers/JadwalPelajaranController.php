<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class JadwalPelajaranController extends Controller
{
    /**
     * Display schedule listing for Admin.
     */
    public function index(Request $request)
    {
        $selectedKelasId = $request->get('kelas_id');
        
        $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        $mapels = MataPelajaran::with('guru')->get();

        $query = JadwalPelajaran::with(['kelas', 'mapel.guru']);

        if ($selectedKelasId) {
            $query->where('kelas_id', $selectedKelasId);
        }

        // Order by day order, then time
        $hariOrder = [
            'Senin' => 1,
            'Selasa' => 2,
            'Rabu' => 3,
            'Kamis' => 4,
            'Jumat' => 5,
            'Sabtu' => 6,
            'Minggu' => 7
        ];

        $jadwal = $query->get()->sortBy(function($item) use ($hariOrder) {
            return ($hariOrder[$item->hari] ?? 9) . '_' . $item->jam_mulai;
        });

        return view('admin.jadwal.index', compact('jadwal', 'kelas', 'mapels', 'selectedKelasId'));
    }

    /**
     * Store a new schedule record (Admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih',
            'hari.required' => 'Hari wajib dipilih',
            'jam_mulai.required' => 'Jam mulai wajib diisi',
            'jam_selesai.required' => 'Jam selesai wajib diisi',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai',
        ]);

        // Check for schedule clash in the same class at the same time
        $clash = JadwalPelajaran::where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function($inner) use ($request) {
                      $inner->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })
            ->first();

        if ($clash) {
            return redirect()->back()->withInput()->with('error', 'Jadwal bentrok! Kelas tersebut sudah memiliki jadwal lain di waktu yang sama.');
        }

        $jadwal = JadwalPelajaran::create($request->all());

        ActivityLog::log(
            'Tambah Jadwal',
            'Membuat jadwal baru di kelas ' . $jadwal->kelas->nama_kelas . ' untuk ' . $jadwal->mapel->nama_mapel
        );

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    /**
     * Update schedule record (Admin).
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
        ]);

        // Check clash excluding current record
        $clash = JadwalPelajaran::where('id', '!=', $id)
            ->where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where(function($q) use ($request) {
                $q->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                  ->orWhere(function($inner) use ($request) {
                      $inner->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                  });
            })
            ->first();

        if ($clash) {
            return redirect()->back()->withInput()->with('error', 'Jadwal bentrok! Kelas tersebut sudah memiliki jadwal lain di waktu yang sama.');
        }

        $jadwal->update($request->all());

        ActivityLog::log(
            'Update Jadwal',
            'Mengubah jadwal ID ' . $id . ' di kelas ' . $jadwal->kelas->nama_kelas
        );

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil diperbarui!');
    }

    /**
     * Delete schedule record (Admin).
     */
    public function destroy($id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        
        ActivityLog::log(
            'Hapus Jadwal',
            'Menghapus jadwal ID ' . $id . ' di kelas ' . $jadwal->kelas->nama_kelas
        );

        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }

    /**
     * Display teaching schedule for the authenticated Guru.
     */
    public function guruIndex()
    {
        $guruId = auth()->id();
        
        // Find subject IDs taught by this Guru
        $mapelIds = MataPelajaran::where('guru_id', $guruId)->pluck('id');

        $jadwal = JadwalPelajaran::with(['kelas', 'mapel'])
            ->whereIn('mapel_id', $mapelIds)
            ->get();

        // Group by day for clean listing
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jadwalGrouped = [];

        foreach ($hariList as $hari) {
            $jadwalGrouped[$hari] = $jadwal->where('hari', $hari)->sortBy('jam_mulai');
        }

        return view('guru.jadwal.index', compact('jadwalGrouped'));
    }

    /**
     * Display class schedule for the authenticated Siswa.
     */
    public function siswaIndex()
    {
        $siswa = auth()->user()->siswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $jadwal = JadwalPelajaran::with(['mapel.guru', 'kelas'])
            ->where('kelas_id', $siswa->kelas_id)
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jadwalGrouped = [];

        foreach ($hariList as $hari) {
            $jadwalGrouped[$hari] = $jadwal->where('hari', $hari)->sortBy('jam_mulai');
        }

        return view('siswa.jadwal.index', compact('jadwalGrouped', 'siswa'));
    }
}
