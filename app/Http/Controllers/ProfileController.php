<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Siswa;
use App\Models\ActivityLog;

class ProfileController extends Controller
{
    /**
     * Tampilkan profil pengguna.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Load siswa relation if user has siswa role
        if ($user->role === 'siswa' && $user->siswa_id) {
            $user->load('siswa.kelas');
        }

        return view('profile', compact('user'));
    }

    /**
     * Perbarui data profil pengguna.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Aturan validasi dasar
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
        ];

        // Tambahan validasi untuk Guru (disimpan di tabel users)
        if ($user->role === 'guru') {
            $rules['telp'] = 'nullable|string|max:20';
            $rules['alamat'] = 'nullable|string|max:500';
        }

        // Tambahan validasi untuk Siswa (disimpan di tabel siswa)
        if ($user->role === 'siswa') {
            $rules['telp'] = 'nullable|string|max:20';
            $rules['alamat'] = 'nullable|string|max:500';
        }

        $request->validate($rules, [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        // Update data dasar user
        $user->name = $request->name;
        $user->email = $request->email;

        if ($user->role === 'guru') {
            $user->telp = $request->telp;
            $user->alamat = $request->alamat;
        }

        $user->save();

        // Jika siswa, update juga tabel siswa
        if ($user->role === 'siswa' && $user->siswa_id) {
            $siswa = Siswa::findOrFail($user->siswa_id);
            $siswa->nama = $request->name;
            $siswa->telp = $request->telp;
            $siswa->alamat = $request->alamat;
            $siswa->save();
        }

        ActivityLog::log('update_profile', 'Memperbarui profil akun.');

        return redirect()->route('profile')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Perbarui password pengguna.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:4|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 4 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        // Cek password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        // Update password baru
        $user->password = Hash::make($request->password);
        $user->save();

        ActivityLog::log('change_password', 'Mengubah password akun.');

        return redirect()->route('profile')->with('success', 'Password Anda berhasil diubah!');
    }
}
