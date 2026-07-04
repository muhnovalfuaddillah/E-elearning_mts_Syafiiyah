<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class TahunAkademikController extends Controller
{
    public function index()
    {
        $tahunAkademik = TahunAkademik::orderBy('tanggal_mulai', 'desc')->paginate(10);
        $editTahunAkademik = null;

        return view('admin.tahun-akademik.index', compact('tahunAkademik', 'editTahunAkademik'));
    }

    public function create()
    {
        return redirect()->route('admin.tahun-akademik.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status_aktif' => 'nullable|boolean',
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');

        $tahunAkademik = TahunAkademik::create($validated);

        if ($tahunAkademik->status_aktif) {
            TahunAkademik::where('id', '!=', $tahunAkademik->id)->update(['status_aktif' => false]);
        }

        ActivityLog::log('create_tahun_akademik', 'Menambahkan tahun akademik: ' . $tahunAkademik->nama_tahun . ' - ' . $tahunAkademik->semester);

        return redirect()->route('admin.tahun-akademik.index')->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $editTahunAkademik = TahunAkademik::findOrFail($id);
        $tahunAkademik = TahunAkademik::orderBy('tanggal_mulai', 'desc')->paginate(10);

        return view('admin.tahun-akademik.index', compact('tahunAkademik', 'editTahunAkademik'));
    }

    public function update(Request $request, $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);

        $validated = $request->validate([
            'nama_tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status_aktif' => 'nullable|boolean',
        ]);

        $validated['status_aktif'] = $request->boolean('status_aktif');
        $tahunAkademik->update($validated);

        if ($tahunAkademik->status_aktif) {
            TahunAkademik::where('id', '!=', $tahunAkademik->id)->update(['status_aktif' => false]);
        }

        ActivityLog::log('update_tahun_akademik', 'Memperbarui tahun akademik: ' . $tahunAkademik->nama_tahun . ' - ' . $tahunAkademik->semester);

        return redirect()->route('admin.tahun-akademik.index')->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);
        $label = $tahunAkademik->nama_tahun . ' - ' . $tahunAkademik->semester;
        $tahunAkademik->delete();

        ActivityLog::log('delete_tahun_akademik', 'Menghapus tahun akademik: ' . $label);

        return redirect()->back()->with('success', 'Tahun akademik berhasil dihapus.');
    }
}
