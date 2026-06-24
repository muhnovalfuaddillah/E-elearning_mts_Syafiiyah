<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Kelas;
use App\Models\User;
use App\Models\AppNotification;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengumumanController extends Controller
{
    /**
     * Display announcements list for Admin and Guru.
     */
    public function index(Request $request)
    {
        $role = auth()->user()->role;
        $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        if ($role === 'admin') {
            $pengumuman = Pengumuman::with(['user', 'kelas'])->latest()->paginate(10);
        } else { // Guru
            // Guru can see school announcements and class announcements they created or class-specific
            $pengumuman = Pengumuman::with(['user', 'kelas'])
                ->where('user_id', auth()->id())
                ->orWhere('tipe', 'sekolah')
                ->latest()
                ->paginate(10);
        }

        return view('admin.pengumuman.index', compact('pengumuman', 'kelas'));
    }

    /**
     * Store a new announcement and dispatch notifications.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tipe' => 'required|in:sekolah,kelas',
            'kelas_id' => 'required_if:tipe,kelas|nullable|exists:kelas,id',
        ], [
            'judul.required' => 'Judul pengumuman wajib diisi',
            'isi.required' => 'Isi pengumuman wajib diisi',
            'tipe.required' => 'Tipe pengumuman wajib dipilih',
            'kelas_id.required_if' => 'Kelas wajib dipilih jika tipe pengumuman adalah Kelas',
        ]);

        DB::beginTransaction();

        try {
            $pengumuman = Pengumuman::create([
                'user_id' => auth()->id(),
                'judul' => $request->judul,
                'isi' => $request->isi,
                'tipe' => $request->tipe,
                'kelas_id' => $request->tipe === 'kelas' ? $request->kelas_id : null,
            ]);

            ActivityLog::log(
                'Buat Pengumuman',
                'Membuat pengumuman "' . $pengumuman->judul . '" tipe ' . $pengumuman->tipe
            );

            // Generate In-App Notifications
            $now = now();
            $notifications = [];
            $detailLink = route('announcements.show-detail', $pengumuman->id);
            $notifTitle = 'Pengumuman Baru';
            $notifMessage = $pengumuman->judul;

            if ($pengumuman->tipe === 'sekolah') {
                // Notifikasi untuk semua user
                $userIds = User::pluck('id');
                foreach ($userIds as $userId) {
                    // Jangan kirim ke pembuat sendiri agar tidak redundant
                    if ($userId == auth()->id()) continue;
                    $notifications[] = [
                        'user_id' => $userId,
                        'title' => $notifTitle,
                        'message' => $notifMessage,
                        'type' => 'pengumuman',
                        'link' => $detailLink,
                        'is_read' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            } else {
                // Notifikasi untuk siswa di kelas terkait
                $userIds = User::where('role', 'siswa')
                    ->whereHas('siswa', function ($query) use ($pengumuman) {
                        $query->where('kelas_id', $pengumuman->kelas_id);
                    })
                    ->pluck('id');

                foreach ($userIds as $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'title' => $notifTitle . ' Kelas',
                        'message' => $notifMessage,
                        'type' => 'pengumuman',
                        'link' => $detailLink,
                        'is_read' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($notifications)) {
                AppNotification::insert($notifications);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Pengumuman berhasil dipublikasikan dan notifikasi telah dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal mempublikasikan pengumuman: ' . $e->getMessage());
        }
    }

    /**
     * Update an announcement.
     */
    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        // Authorization check
        if (auth()->user()->role !== 'admin' && $pengumuman->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tipe' => 'required|in:sekolah,kelas',
            'kelas_id' => 'required_if:tipe,kelas|nullable|exists:kelas,id',
        ]);

        $pengumuman->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'tipe' => $request->tipe,
            'kelas_id' => $request->tipe === 'kelas' ? $request->kelas_id : null,
        ]);

        ActivityLog::log(
            'Update Pengumuman',
            'Mengubah pengumuman "' . $pengumuman->judul . '"'
        );

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Delete an announcement.
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        // Authorization check
        if (auth()->user()->role !== 'admin' && $pengumuman->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        ActivityLog::log(
            'Hapus Pengumuman',
            'Menghapus pengumuman "' . $pengumuman->judul . '"'
        );

        $pengumuman->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Generic detail viewer for announcements, accessible by all roles.
     */
    public function showDetail($id)
    {
        $pengumuman = Pengumuman::with(['user', 'kelas'])->findOrFail($id);
        $user = auth()->user();

        // Security check for class announcements
        if ($pengumuman->tipe === 'kelas') {
            if ($user->role === 'siswa') {
                $siswa = $user->siswa;
                if (!$siswa || $siswa->kelas_id !== $pengumuman->kelas_id) {
                    abort(403, 'Anda tidak memiliki akses ke pengumuman kelas ini.');
                }
            }
        }

        // Mark corresponding notification as read if accessed via notification link
        AppNotification::where('user_id', $user->id)
            ->where('link', request()->url())
            ->update(['is_read' => true]);

        return view('admin.pengumuman.show', compact('pengumuman'));
    }

    /**
     * Display announcements list for Siswa.
     */
    public function siswaIndex()
    {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $pengumuman = Pengumuman::with(['user'])
            ->where('tipe', 'sekolah')
            ->orWhere(function($query) use ($siswa) {
                $query->where('tipe', 'kelas')
                      ->where('kelas_id', $siswa->kelas_id);
            })
            ->latest()
            ->paginate(8);

        return view('siswa.pengumuman.index', compact('pengumuman'));
    }
}
