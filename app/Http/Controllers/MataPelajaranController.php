<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MataPelajaran::with('guru');

        // Fitur pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_mapel', 'like', "%{$search}%")
                  ->orWhere('nama_mapel', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $mapel = $query->paginate(5)->withQueryString();

        // Ambil semua data Guru untuk dropdown form
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        return view('admin.mata-pelajaran.index', compact('mapel', 'gurus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'kode_mapel' => 'required|unique:mata_pelajaran,kode_mapel|max:20',
            'nama_mapel' => 'required|max:100',
            'guru_id' => 'nullable|exists:users,id',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi',
            'guru_id.exists' => 'Guru pengampu tidak valid',
        ]);

        // Simpan data
        MataPelajaran::create($request->all());

        return redirect()->route('admin.mata-pelajaran.index')
                        ->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        // Validasi data
        $request->validate([
            'kode_mapel' => 'required|max:20|unique:mata_pelajaran,kode_mapel,' . $id,
            'nama_mapel' => 'required|max:100',
            'guru_id' => 'nullable|exists:users,id',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi',
            'guru_id.exists' => 'Guru pengampu tidak valid',
        ]);

        $mapel->update($request->all());

        return redirect()->route('admin.mata-pelajaran.index')
                        ->with('success', 'Mata Pelajaran berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $mapel = MataPelajaran::findOrFail($id);
        $mapel->delete();

        return redirect()->route('admin.mata-pelajaran.index')
                        ->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}
