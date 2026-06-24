<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Display a listing of all attendance records (Rekap Absensi).
     */
    public function index(Request $request)
    {
        $query = Absensi::with('siswa.kelas', 'guru', 'mapel');

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
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan mapel
        if ($request->has('mapel_id') && !empty($request->mapel_id)) {
            $query->where('mapel_id', $request->mapel_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $absensi = $query->paginate(10)->withQueryString();

        // Ambil data untuk form filter
        $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        $mapels = \App\Models\MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        return view('admin.absensi.index', compact('absensi', 'kelas', 'mapels'));
    }

    /**
     * Remove the specified resource from storage (Reset Absen).
     */
    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return redirect()->route('admin.absensi.index')
                        ->with('success', 'Data absensi berhasil direset / dihapus!');
    }

    /**
     * Export rekap absensi terfilter ke CSV.
     */
    public function export(Request $request)
    {
        $query = Absensi::with('siswa.kelas', 'guru', 'mapel');

        // Terapkan filter yang sama
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }
        if ($request->has('kelas_id') && !empty($request->kelas_id)) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->where('tanggal', '>=', $request->start_date);
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->where('tanggal', '<=', $request->end_date);
        }
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        if ($request->has('mapel_id') && !empty($request->mapel_id)) {
            $query->where('mapel_id', $request->mapel_id);
        }

        $records = $query->orderBy('tanggal', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=rekap_absensi_sekolah.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Tanggal', 'Jam Absen', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Status Kehadiran', 'Guru Pencatat', 'Keterangan'];

        $callback = function() use($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            $statusMap = [
                'H' => 'Hadir',
                'S' => 'Sakit',
                'I' => 'Izin',
                'A' => 'Alpa'
            ];

            foreach ($records as $index => $record) {
                fputcsv($file, [
                    $index + 1,
                    $record->tanggal,
                    $record->created_at ? $record->created_at->format('H:i') : '-',
                    $record->siswa->nis ?? '-',
                    $record->siswa->nama ?? '-',
                    $record->kelas->kode_kelas ?? '-',
                    $record->mapel->nama_mapel ?? '-',
                    $statusMap[$record->status] ?? '-',
                    $record->guru->name ?? 'Sistem (QR Code)',
                    $record->keterangan ?? '-'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
