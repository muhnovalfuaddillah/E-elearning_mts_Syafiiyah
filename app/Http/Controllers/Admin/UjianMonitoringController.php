<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ujian;
use Illuminate\Http\Request;

class UjianMonitoringController extends Controller
{
    /**
     * Tampilkan halaman monitoring pembuatan ujian dan soal oleh Guru.
     */
    public function index(Request $request)
    {
        // Ambil semua user dengan role guru beserta ujian yang mereka buat
        $query = User::where('role', 'guru')->with(['mataPelajarans']);

        // Filter pencarian nama guru
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $gurus = $query->orderBy('name', 'asc')->get();

        // Untuk setiap guru, ambil ujian yang dibuat beserta jumlah soalnya
        foreach ($gurus as $guru) {
            $guru->ujians = Ujian::where('guru_id', $guru->id)
                ->with('kelas', 'mapel')
                ->withCount('soals')
                ->get();
            
            // Tandai status apakah guru sudah membuat ujian dan mengisi soal
            $guru->has_ujian = $guru->ujians->isNotEmpty();
            $guru->has_empty_soal = $guru->ujians->contains('soals_count', 0);
            $guru->total_soal_dibuat = $guru->ujians->sum('soals_count');
        }

        return view('admin.ujian.monitoring', compact('gurus'));
    }
}
