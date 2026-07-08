<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query dengan pencarian
        $query = Kelas::query();
        
        // Fitur pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kode_kelas', 'like', "%{$search}%")
                  ->orWhere('tingkat', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }
        
        // Filter berdasarkan tingkat
        if ($request->has('tingkat') && !empty($request->tingkat)) {
            $query->where('tingkat', $request->tingkat);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $kelas = $query->paginate(5)->withQueryString();
        
        return view('admin.kelas.index', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'kode_kelas' => 'required|unique:kelas|max:20',
            'nama_kelas' => 'required|max:100',
            'tingkat' => 'required|in:7,8,9',
            'jurusan' => 'required|max:50',
        ], [
            'kode_kelas.required' => 'Kode kelas wajib diisi',
            'kode_kelas.unique' => 'Kode kelas sudah digunakan',
            'nama_kelas.required' => 'Nama kelas wajib diisi',
            'tingkat.required' => 'Tingkat wajib dipilih',
            'jurusan.required' => 'Jurusan wajib diisi'
        ]);

        // Create data
        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Kelas berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)  // PERBAIKAN: parameter $id
    {
        $kelas = Kelas::findOrFail($id);
        
        // Validasi data
        $request->validate([
            'kode_kelas' => 'required|max:20|unique:kelas,kode_kelas,' . $id,
            'nama_kelas' => 'required|max:100',
            'tingkat' => 'required|in:7,8,9',
            'jurusan' => 'required|max:50',
        ], [
            'kode_kelas.required' => 'Kode kelas wajib diisi',
            'kode_kelas.unique' => 'Kode kelas sudah digunakan',
            'nama_kelas.required' => 'Nama kelas wajib diisi',
            'tingkat.required' => 'Tingkat wajib dipilih',
            'jurusan.required' => 'Jurusan wajib diisi'
        ]);

        // Update data
        $kelas->update($request->all());

        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Kelas berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)  // PERBAIKAN: parameter $id
    {
        $kelas = Kelas::findOrFail($id);
        
        // Cek apakah kelas memiliki relasi dengan data lain
        // if ($kelas->siswa()->count() > 0) {
        //     return redirect()->route('admin.kelas.index')
        //                     ->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa!');
        // }
        
        $kelas->delete();
        
        return redirect()->route('admin.kelas.index')
                        ->with('success', 'Kelas berhasil dihapus!');
    }
}