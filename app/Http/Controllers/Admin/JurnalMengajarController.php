<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class JurnalMengajarController extends Controller
{
    /**
     * Tampilkan semua jurnal mengajar guru dengan filter.
     */
    public function index(Request $request)
    {
        $query = JurnalMengajar::with('guru', 'kelas', 'mapel');

        // Fetch data untuk Filter Dropdown
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();
        $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        // Apply filters
        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }
        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.jurnal.index', compact('jurnals', 'gurus', 'kelas', 'mapels'));
    }

    /**
     * Tampilkan detail jurnal mengajar.
     */
    public function show($id)
    {
        $jurnal = JurnalMengajar::with('guru', 'kelas', 'mapel')->findOrFail($id);
        return view('admin.jurnal.show', compact('jurnal'));
    }

    /**
     * Tampilkan halaman rekap jurnal untuk dicetak oleh admin.
     */
    public function rekap(Request $request)
    {
        $query = JurnalMengajar::with('guru', 'kelas', 'mapel');

        // Apply filters
        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }

        $jurnals = $query->orderBy('tanggal', 'asc')->orderBy('pertemuan_ke', 'asc')->get();
        
        $guruFilter = $request->filled('guru_id') ? User::find($request->guru_id) : null;
        $kelasFilter = $request->filled('kelas_id') ? Kelas::find($request->kelas_id) : null;
        $mapelFilter = $request->filled('mapel_id') ? MataPelajaran::find($request->mapel_id) : null;
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        return view('admin.jurnal.rekap', compact('jurnals', 'guruFilter', 'kelasFilter', 'mapelFilter', 'tanggalMulai', 'tanggalSelesai'));
    }

    /**
     * Hapus jurnal mengajar (oleh admin).
     */
    public function destroy($id)
    {
        $jurnal = JurnalMengajar::findOrFail($id);
        $jurnal->delete();

        return redirect()->route('admin.jurnal.index')->with('success', 'Jurnal mengajar berhasil dihapus oleh Administrator!');
    }
}
