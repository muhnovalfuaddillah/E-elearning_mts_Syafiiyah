<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;

class JurnalMengajarController extends Controller
{
    /**
     * Tampilkan daftar jurnal mengajar milik guru.
     */
    public function index(Request $request)
    {
        $guruId = auth()->id();
        $query = JurnalMengajar::with('kelas', 'mapel')->where('guru_id', $guruId);

        // Fetch Kelas & Mapel yang diampu untuk Filter
        $mapels = MataPelajaran::where('guru_id', $guruId)->get();
        $mapelIds = $mapels->pluck('id');
        $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        // Apply filters
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }
        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('pertemuan_ke', 'desc')->paginate(10)->withQueryString();

        return view('guru.jurnal.index', compact('jurnals', 'kelas', 'mapels'));
    }

    /**
     * Tampilkan form tambah jurnal.
     */
    public function create()
    {
        $guruId = auth()->id();
        $mapels = MataPelajaran::where('guru_id', $guruId)->get();
        $mapelIds = $mapels->pluck('id');
        $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('guru.jurnal.create', compact('kelas', 'mapels'));
    }

    /**
     * Simpan jurnal mengajar baru.
     */
    public function store(Request $request)
    {
        $guruId = auth()->id();

        // Get allowed Mapels & Kelas for validation
        $allowedMapelIds = MataPelajaran::where('guru_id', $guruId)->pluck('id')->toArray();
        $allowedKelasIds = JadwalPelajaran::whereIn('mapel_id', $allowedMapelIds)->pluck('kelas_id')->unique()->toArray();

        $request->validate([
            'kelas_id' => [
                'required',
                'exists:kelas,id',
                function ($attribute, $value, $fail) use ($allowedKelasIds) {
                    if (!in_array($value, $allowedKelasIds)) {
                        $fail('Anda tidak memiliki wewenang mengajar di kelas ini.');
                    }
                }
            ],
            'mapel_id' => [
                'required',
                'exists:mata_pelajaran,id',
                function ($attribute, $value, $fail) use ($allowedMapelIds) {
                    if (!in_array($value, $allowedMapelIds)) {
                        $fail('Anda tidak mengampu mata pelajaran ini.');
                    }
                }
            ],
            'tanggal' => 'required|date',
            'pertemuan_ke' => 'required|integer|min:1',
            'materi' => 'required|string|max:500',
            'kegiatan' => 'required|string|max:1000',
            'catatan' => 'nullable|string|max:500',
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'pertemuan_ke.required' => 'Pertemuan ke- wajib diisi',
            'materi.required' => 'Materi pembelajaran wajib diisi',
            'kegiatan.required' => 'Kegiatan pembelajaran wajib diisi',
        ]);

        JurnalMengajar::create([
            'guru_id' => $guruId,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'tanggal' => $request->tanggal,
            'pertemuan_ke' => $request->pertemuan_ke,
            'materi' => $request->materi,
            'kegiatan' => $request->kegiatan,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('guru.jurnal.index')->with('success', 'Jurnal mengajar berhasil disimpan!');
    }

    /**
     * Tampilkan form edit jurnal.
     */
    public function edit($id)
    {
        $guruId = auth()->id();
        $jurnal = JurnalMengajar::where('guru_id', $guruId)->findOrFail($id);

        $mapels = MataPelajaran::where('guru_id', $guruId)->get();
        $mapelIds = $mapels->pluck('id');
        $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('guru.jurnal.edit', compact('jurnal', 'kelas', 'mapels'));
    }

    /**
     * Perbarui jurnal mengajar.
     */
    public function update(Request $request, $id)
    {
        $guruId = auth()->id();
        $jurnal = JurnalMengajar::where('guru_id', $guruId)->findOrFail($id);

        $allowedMapelIds = MataPelajaran::where('guru_id', $guruId)->pluck('id')->toArray();
        $allowedKelasIds = JadwalPelajaran::whereIn('mapel_id', $allowedMapelIds)->pluck('kelas_id')->unique()->toArray();

        $request->validate([
            'kelas_id' => [
                'required',
                'exists:kelas,id',
                function ($attribute, $value, $fail) use ($allowedKelasIds) {
                    if (!in_array($value, $allowedKelasIds)) {
                        $fail('Anda tidak memiliki wewenang mengajar di kelas ini.');
                    }
                }
            ],
            'mapel_id' => [
                'required',
                'exists:mata_pelajaran,id',
                function ($attribute, $value, $fail) use ($allowedMapelIds) {
                    if (!in_array($value, $allowedMapelIds)) {
                        $fail('Anda tidak mengampu mata pelajaran ini.');
                    }
                }
            ],
            'tanggal' => 'required|date',
            'pertemuan_ke' => 'required|integer|min:1',
            'materi' => 'required|string|max:500',
            'kegiatan' => 'required|string|max:1000',
            'catatan' => 'nullable|string|max:500',
        ], [
            'kelas_id.required' => 'Kelas wajib dipilih',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih',
            'tanggal.required' => 'Tanggal wajib diisi',
            'pertemuan_ke.required' => 'Pertemuan ke- wajib diisi',
            'materi.required' => 'Materi pembelajaran wajib diisi',
            'kegiatan.required' => 'Kegiatan pembelajaran wajib diisi',
        ]);

        $jurnal->update([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'tanggal' => $request->tanggal,
            'pertemuan_ke' => $request->pertemuan_ke,
            'materi' => $request->materi,
            'kegiatan' => $request->kegiatan,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('guru.jurnal.index')->with('success', 'Jurnal mengajar berhasil diperbarui!');
    }

    /**
     * Hapus jurnal mengajar.
     */
    public function destroy($id)
    {
        $guruId = auth()->id();
        $jurnal = JurnalMengajar::where('guru_id', $guruId)->findOrFail($id);
        $jurnal->delete();

        return redirect()->route('guru.jurnal.index')->with('success', 'Jurnal mengajar berhasil dihapus!');
    }
}
