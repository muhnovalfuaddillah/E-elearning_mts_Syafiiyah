<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'guru');

        // Fitur pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('mapel', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan jenis kelamin
        if ($request->has('jenis_kelamin') && !empty($request->jenis_kelamin)) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $gurus = $query->paginate(5)->withQueryString();

        return view('admin.guru.index', compact('gurus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
            'nip' => 'nullable|unique:users,nip|max:30',
            'mapel' => 'nullable|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'telp' => 'nullable|max:20',
            'alamat' => 'nullable',
        ], [
            'name.required' => 'Nama guru wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan oleh user lain',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 4 karakter',
            'nip.unique' => 'NIP sudah terdaftar',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
        ]);

        // Simpan data dengan role 'guru'
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'nip' => $request->nip,
            'mapel' => $request->mapel,
            'jenis_kelamin' => $request->jenis_kelamin,
            'telp' => $request->telp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);

        // Validasi data
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:4',
            'nip' => 'nullable|max:30|unique:users,nip,' . $id,
            'mapel' => 'nullable|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'telp' => 'nullable|max:20',
            'alamat' => 'nullable',
        ], [
            'name.required' => 'Nama guru wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan oleh user lain',
            'password.min' => 'Password minimal 4 karakter',
            'nip.unique' => 'NIP sudah terdaftar',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'mapel' => $request->mapel,
            'jenis_kelamin' => $request->jenis_kelamin,
            'telp' => $request->telp,
            'alamat' => $request->alamat,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $guru->update($data);

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);
        $guru->delete();

        return redirect()->route('admin.guru.index')
                        ->with('success', 'Data Guru berhasil dihapus!');
    }
}
