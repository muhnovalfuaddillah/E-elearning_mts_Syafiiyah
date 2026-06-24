<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    /**
     * Display a listing of all student grades (Rekap Nilai).
     */
    public function index(Request $request)
    {
        $query = Penilaian::with('siswa.kelas', 'mapel');

        // Fitur pencarian nama siswa atau NIS
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas_id') && !empty($request->kelas_id)) {
            $kelasId = $request->kelas_id;
            $query->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        // Filter berdasarkan mapel
        if ($request->has('mapel_id') && !empty($request->mapel_id)) {
            $query->where('mapel_id', $request->mapel_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $penilaian = $query->paginate(10)->withQueryString();

        // Ambil data untuk form filter
        $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        return view('admin.penilaian.index', compact('penilaian', 'kelas', 'mapels'));
    }

    /**
     * Remove the specified resource from storage (Reset Nilai).
     */
    public function destroy($id)
    {
        $penilaian = Penilaian::findOrFail($id);
        $penilaian->delete();

        return redirect()->route('admin.penilaian.index')
                        ->with('success', 'Data nilai berhasil direset / dihapus!');
    }
}
