<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Scope hanya materi yang diunggah oleh Guru yang login
        $query = Materi::where('user_id', auth()->id())->with('kelas', 'mapel');

        // Fitur pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas_id') && !empty($request->kelas_id)) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan mapel
        if ($request->has('mapel_id') && !empty($request->mapel_id)) {
            $query->where('mapel_id', $request->mapel_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $materi = $query->paginate(5)->withQueryString();

        // Ambil mata pelajaran yang diajar guru tersebut
        $mapels = MataPelajaran::where('guru_id', auth()->id())->get();
        $mapelIds = $mapels->pluck('id');

        // Ambil kelas yang diajar oleh guru berdasarkan jadwal pelajaran
        $kelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('guru.materi.index', compact('materi', 'kelas', 'mapels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'file_materi' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx,zip,rar,png,jpg,jpeg|max:10240', // Max 10MB
            'link_video' => 'nullable|url',
        ], [
            'judul.required' => 'Judul materi wajib diisi',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih',
            'mapel_id.exists' => 'Mata pelajaran tidak valid',
            'file_materi.mimes' => 'Format file tidak didukung',
            'file_materi.max' => 'Ukuran file maksimal adalah 10MB',
            'link_video.url' => 'Format tautan video tidak valid (gunakan URL lengkap)',
        ]);

        // Verifikasi bahwa mapel_id diajar oleh guru yang sedang login
        $allowedMapels = MataPelajaran::where('guru_id', auth()->id())->pluck('id')->toArray();
        if (!in_array($request->mapel_id, $allowedMapels)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk menambahkan materi pada mata pelajaran ini.');
        }

        // Verifikasi bahwa kelas_id terhubung dengan jadwal pelajaran guru yang sedang login
        $allowedKelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $allowedMapels)->pluck('kelas_id')->unique()->toArray();
        if (!in_array($request->kelas_id, $allowedKelasIds)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk menambahkan materi pada kelas ini.');
        }

        if (!$request->hasFile('file_materi') && !$request->filled('link_video') && !$request->filled('deskripsi')) {
            return redirect()->back()->withErrors(['konten' => 'Harus ada file lampiran, tautan video, atau deskripsi teks yang diisi.'])->withInput();
        }

        // Klasifikasi tipe materi secara otomatis
        $tipe = 'other';
        $filePath = null;

        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('materi', $filename, 'public');

            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension == 'pdf') {
                $tipe = 'pdf';
            } elseif (in_array($extension, ['ppt', 'pptx'])) {
                $tipe = 'ppt';
            }
        } elseif ($request->filled('link_video')) {
            $tipe = 'video';
        }

        $materi = Materi::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'user_id' => auth()->id(),
            'file_materi' => $filePath,
            'link_video' => $request->link_video,
            'tipe' => $tipe,
        ]);

        // Kirim In-App Notification ke seluruh siswa di kelas tersebut
        $mapel = \App\Models\MataPelajaran::find($request->mapel_id);
        $now = now();
        $notifications = [];
        $materiLink = route('siswa.materi.index');
        
        $userIds = \App\Models\User::where('role', 'siswa')
            ->whereHas('siswa', function ($query) use ($materi) {
                $query->where('kelas_id', $materi->kelas_id);
            })
            ->pluck('id');

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => 'Materi Baru: ' . ($mapel ? $mapel->nama_mapel : 'Pelajaran'),
                'message' => $materi->judul,
                'type' => 'materi',
                'link' => $materiLink,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        if (!empty($notifications)) {
            \App\Models\AppNotification::insert($notifications);
        }

        return redirect()->route('guru.materi.index')
                        ->with('success', 'Materi Pelajaran berhasil diterbitkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $materi = Materi::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'file_materi' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx,zip,rar,png,jpg,jpeg|max:10240',
            'link_video' => 'nullable|url',
        ], [
            'judul.required' => 'Judul materi wajib diisi',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih',
            'mapel_id.exists' => 'Mata pelajaran tidak valid',
            'file_materi.mimes' => 'Format file tidak didukung',
            'file_materi.max' => 'Ukuran file maksimal adalah 10MB',
            'link_video.url' => 'Format tautan video tidak valid',
        ]);

        // Verifikasi bahwa mapel_id diajar oleh guru yang sedang login
        $allowedMapels = MataPelajaran::where('guru_id', auth()->id())->pluck('id')->toArray();
        if (!in_array($request->mapel_id, $allowedMapels)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk memperbarui materi pada mata pelajaran ini.');
        }

        // Verifikasi bahwa kelas_id terhubung dengan jadwal pelajaran guru yang sedang login
        $allowedKelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $allowedMapels)->pluck('kelas_id')->unique()->toArray();
        if (!in_array($request->kelas_id, $allowedKelasIds)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk memperbarui materi pada kelas ini.');
        }

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'link_video' => $request->link_video,
        ];

        if ($request->filled('link_video') && !$materi->file_materi && !$request->hasFile('file_materi')) {
            $data['tipe'] = 'video';
        }

        if ($request->hasFile('file_materi')) {
            // Hapus file lama dari storage
            if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
                Storage::disk('public')->delete($materi->file_materi);
            }

            $file = $request->file('file_materi');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $data['file_materi'] = $file->storeAs('materi', $filename, 'public');

            // Set tipe file baru
            $extension = strtolower($file->getClientOriginalExtension());
            if ($extension == 'pdf') {
                $data['tipe'] = 'pdf';
            } elseif (in_array($extension, ['ppt', 'pptx'])) {
                $data['tipe'] = 'ppt';
            } else {
                $data['tipe'] = 'other';
            }
        }

        $materi->update($data);

        return redirect()->route('guru.materi.index')
                        ->with('success', 'Materi Pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $materi = Materi::where('user_id', auth()->id())->findOrFail($id);

        // Hapus file dari storage
        if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
            Storage::disk('public')->delete($materi->file_materi);
        }

        $materi->delete();

        return redirect()->route('guru.materi.index')
                        ->with('success', 'Materi Pelajaran berhasil dihapus!');
    }
}
