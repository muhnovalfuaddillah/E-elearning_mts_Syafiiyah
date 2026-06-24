<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class KelasSiswaController extends Controller
{
    /**
     * Display listing of classes with student counts.
     */
    public function kelasIndex()
    {
        $mapelIds = \App\Models\MataPelajaran::where('guru_id', auth()->id())->pluck('id');
        $kelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();

        $kelas = Kelas::whereIn('id', $kelasIds)
            ->withCount('siswa')
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('guru.kelas.index', compact('kelas'));
    }

    /**
     * Display listing of students in a specific class.
     */
    public function siswaIndex($kelasId)
    {
        $mapelIds = \App\Models\MataPelajaran::where('guru_id', auth()->id())->pluck('id');
        $allowedKelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique()->toArray();

        if (!in_array($kelasId, $allowedKelasIds)) {
            return redirect()->route('guru.kelas.index')->with('error', 'Anda tidak memiliki wewenang untuk melihat data siswa di kelas ini.');
        }

        $kelasItem = Kelas::withCount('siswa')->findOrFail($kelasId);
        
        $siswa = Siswa::where('kelas_id', $kelasId)
            ->orderBy('nama', 'asc')
            ->get();

        return view('guru.kelas.siswa', compact('kelasItem', 'siswa'));
    }
}
