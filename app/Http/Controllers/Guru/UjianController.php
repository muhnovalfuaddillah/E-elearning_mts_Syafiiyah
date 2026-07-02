<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\UjianSoal;
use App\Models\UjianSiswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UjianController extends Controller
{
    /**
     * Tampilkan daftar ujian yang dibuat oleh guru.
     */
    public function index(Request $request)
    {
        $query = Ujian::where('guru_id', auth()->id())->with('kelas', 'mapel');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('judul', 'like', "%{$search}%");
        }

        if ($request->has('kelas_id') && !empty($request->kelas_id)) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $ujians = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Ambil mapel dan kelas yang diajar
        $mapels = MataPelajaran::where('guru_id', auth()->id())->get();
        $mapelIds = $mapels->pluck('id');
        $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('guru.ujian.index', compact('ujians', 'kelas', 'mapels'));
    }

    /**
     * Tampilkan form tambah ujian.
     */
    public function create()
    {
        $mapels = MataPelajaran::where('guru_id', auth()->id())->get();
        $mapelIds = $mapels->pluck('id');
        $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('guru.ujian.create', compact('kelas', 'mapels'));
    }

    /**
     * Simpan ujian baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'durasi' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,closed',
        ]);

        $ujian = Ujian::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'guru_id' => auth()->id(),
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $request->durasi,
            'status' => $request->status,
        ]);

        ActivityLog::log('create_ujian', 'Guru membuat ujian baru: ' . $ujian->judul);

        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dibuat. Silakan tambahkan soal.');
    }

    /**
     * Tampilkan form edit ujian.
     */
    public function edit($id)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->findOrFail($id);
        $mapels = MataPelajaran::where('guru_id', auth()->id())->get();
        $mapelIds = $mapels->pluck('id');
        $kelasIds = JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('guru.ujian.edit', compact('ujian', 'kelas', 'mapels'));
    }

    /**
     * Update data ujian.
     */
    public function update(Request $request, $id)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->findOrFail($id);

        $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'durasi' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,closed',
        ]);

        $ujian->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $request->durasi,
            'status' => $request->status,
        ]);

        ActivityLog::log('update_ujian', 'Guru memperbarui ujian: ' . $ujian->judul);

        return redirect()->route('guru.ujian.index')->with('success', 'Informasi ujian berhasil diperbarui.');
    }

    /**
     * Hapus ujian.
     */
    public function destroy($id)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->findOrFail($id);
        $ujian->delete();

        ActivityLog::log('delete_ujian', 'Guru menghapus ujian: ' . $ujian->judul);

        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }

    /**
     * Halaman kelola soal ujian.
     */
    public function soal($id)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->with('soals')->findOrFail($id);
        return view('guru.ujian.soal', compact('ujian'));
    }

    /**
     * Simpan soal baru.
     */
    public function storeSoal(Request $request, $ujianId)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->findOrFail($ujianId);

        $request->validate([
            'pertanyaan' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'opsi_e' => 'nullable',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $gambarPath = $file->storeAs('ujian_soals', $filename, 'public');
        }

        UjianSoal::create([
            'ujian_id' => $ujian->id,
            'pertanyaan' => $request->pertanyaan,
            'gambar' => $gambarPath,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'kunci_jawaban' => $request->kunci_jawaban,
        ]);

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Update soal.
     */
    public function updateSoal(Request $request, $soalId)
    {
        $soal = UjianSoal::findOrFail($soalId);
        $ujian = Ujian::where('guru_id', auth()->id())->findOrFail($soal->ujian_id);

        $request->validate([
            'pertanyaan' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'opsi_e' => 'nullable',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
        ]);

        $gambarPath = $soal->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($soal->gambar) {
                Storage::disk('public')->delete($soal->gambar);
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $gambarPath = $file->storeAs('ujian_soals', $filename, 'public');
        }

        $soal->update([
            'pertanyaan' => $request->pertanyaan,
            'gambar' => $gambarPath,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'kunci_jawaban' => $request->kunci_jawaban,
        ]);

        return redirect()->back()->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Hapus soal.
     */
    public function destroySoal($soalId)
    {
        $soal = UjianSoal::findOrFail($soalId);
        $ujian = Ujian::where('guru_id', auth()->id())->findOrFail($soal->ujian_id);
        
        // Hapus gambar dari storage jika ada
        if ($soal->gambar) {
            Storage::disk('public')->delete($soal->gambar);
        }

        $soal->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Halaman hasil pengerjaan ujian siswa.
     */
    public function hasil($id)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->findOrFail($id);
        
        // Ambil semua siswa di kelas tersebut beserta sesi ujiannya jika ada
        $siswas = \App\Models\Siswa::where('kelas_id', $ujian->kelas_id)
            ->orderBy('nama', 'asc')
            ->get();

        $ujianSiswas = UjianSiswa::where('ujian_id', $ujian->id)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.ujian.hasil', compact('ujian', 'siswas', 'ujianSiswas'));
    }

    /**
     * Cetak Rekap Nilai Ujian (PDF / Print).
     */
    public function exportPdf($id)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->with('kelas', 'mapel')->findOrFail($id);
        
        $siswas = \App\Models\Siswa::where('kelas_id', $ujian->kelas_id)
            ->orderBy('nama', 'asc')
            ->get();

        $ujianSiswas = UjianSiswa::where('ujian_id', $ujian->id)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.ujian.hasil-pdf', compact('ujian', 'siswas', 'ujianSiswas'));
    }

    /**
     * Download Rekap Nilai Ujian (Word / .doc).
     */
    public function exportWord($id)
    {
        $ujian = Ujian::where('guru_id', auth()->id())->with('kelas', 'mapel')->findOrFail($id);
        
        $siswas = \App\Models\Siswa::where('kelas_id', $ujian->kelas_id)
            ->orderBy('nama', 'asc')
            ->get();

        $ujianSiswas = UjianSiswa::where('ujian_id', $ujian->id)
            ->get()
            ->keyBy('siswa_id');

        $headers = [
            "Content-type"        => "application/vnd.ms-word",
            "Content-Disposition" => "attachment;Filename=rekap_nilai_ujian_" . $ujian->id . ".doc",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        return response(view('guru.ujian.hasil-word', compact('ujian', 'siswas', 'ujianSiswas')), 200, $headers);
    }
}
