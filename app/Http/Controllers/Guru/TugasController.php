<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tugas::where('guru_id', auth()->id())->with('kelas', 'mapel');

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

        // Sorting
        $sortBy = $request->get('sort_by', 'deadline');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $tugas = $query->paginate(5)->withQueryString();

        // Ambil mata pelajaran yang diajar guru tersebut
        $mapels = MataPelajaran::where('guru_id', auth()->id())->get();
        $mapelIds = $mapels->pluck('id');
        
        // Ambil kelas yang diajar oleh guru berdasarkan jadwal pelajaran
        $kelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $mapelIds)->pluck('kelas_id')->unique();
        $kelas = Kelas::whereIn('id', $kelasIds)->orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('guru.tugas.index', compact('tugas', 'kelas', 'mapels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'deadline' => 'required|date|after:now',
            'file_tugas' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,png|max:5120', // Max 5MB
        ], [
            'judul.required' => 'Judul tugas wajib diisi',
            'deskripsi.required' => 'Deskripsi / petunjuk tugas wajib diisi',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih',
            'mapel_id.exists' => 'Mata pelajaran tidak valid',
            'deadline.required' => 'Tenggat waktu wajib diisi',
            'deadline.after' => 'Tenggat waktu harus di masa depan',
            'file_tugas.mimes' => 'Format file tidak didukung (gunakan PDF, Word, Excel, PPT, ZIP, JPG, PNG)',
        ]);

        // Verifikasi bahwa mapel_id diajar oleh guru yang sedang login
        $allowedMapels = MataPelajaran::where('guru_id', auth()->id())->pluck('id')->toArray();
        if (!in_array($request->mapel_id, $allowedMapels)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk menambahkan tugas pada mata pelajaran ini.');
        }

        // Verifikasi bahwa kelas_id terhubung dengan jadwal pelajaran guru yang sedang login
        $allowedKelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $allowedMapels)->pluck('kelas_id')->unique()->toArray();
        if (!in_array($request->kelas_id, $allowedKelasIds)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk menambahkan tugas pada kelas ini.');
        }

        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $file = $request->file('file_tugas');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('tugas', $filename, 'public');
        }

        $tugas = Tugas::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'guru_id' => auth()->id(),
            'deadline' => $request->deadline,
            'file_tugas' => $filePath,
        ]);

        $mapel = \App\Models\MataPelajaran::find($request->mapel_id);
        $kelas = \App\Models\Kelas::find($request->kelas_id);
        $guruName = auth()->user()->name;

        // Kirim In-App Notification ke seluruh siswa di kelas tersebut
        $now = now();
        $notifications = [];
        $tugasLink = route('siswa.tugas.index');
        
        $userIds = \App\Models\User::where('role', 'siswa')
            ->whereHas('siswa', function ($query) use ($tugas) {
                $query->where('kelas_id', $tugas->kelas_id);
            })
            ->pluck('id');

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => 'Tugas Baru: ' . ($mapel ? $mapel->nama_mapel : 'Pelajaran'),
                'message' => $tugas->judul . ' (Batas: ' . \Carbon\Carbon::parse($tugas->deadline)->format('d M, H:i') . ')',
                'type' => 'tugas',
                'link' => $tugasLink,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        if (!empty($notifications)) {
            \App\Models\AppNotification::insert($notifications);
        }

        // Kirim WhatsApp Broadcast ke seluruh siswa di kelas tersebut
        $students = \App\Models\Siswa::where('kelas_id', $request->kelas_id)->get();

        if ($students->isNotEmpty() && $mapel && $kelas) {
            $deadlineFormatted = \Carbon\Carbon::parse($request->deadline)->format('d M Y, H:i');
            foreach ($students as $student) {
                if ($student->telp) {
                    $msg = "Tugas Baru MTs Syafiiyah:\n"
                         . "Guru *{$guruName}* menerbitkan tugas baru:\n"
                         . "• *Judul*: {$request->judul}\n"
                         . "• *Mapel*: {$mapel->nama_mapel}\n"
                         . "• *Kelas*: {$kelas->kode_kelas}\n"
                         . "• *Batas Pengumpulan*: {$deadlineFormatted} WIB\n\n"
                         . "Segera login ke aplikasi Pembelajaran Digital untuk mengunduh soal dan mengumpulkan tugas Anda.";
                    
                    \App\Services\FonnteService::send($student->telp, $msg);
                }
            }
        }

        return redirect()->route('guru.tugas.index')
                        ->with('success', 'Tugas berhasil dipublikasikan dan notifikasi WhatsApp telah dikirim!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tugas = Tugas::where('guru_id', auth()->id())->findOrFail($id);

        // Validasi data
        $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'deadline' => 'required|date',
            'file_tugas' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,png|max:5120',
        ], [
            'judul.required' => 'Judul tugas wajib diisi',
            'deskripsi.required' => 'Deskripsi / petunjuk tugas wajib diisi',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'mapel_id.required' => 'Mata pelajaran wajib dipilih',
            'mapel_id.exists' => 'Mata pelajaran tidak valid',
            'deadline.required' => 'Tenggat waktu wajib diisi',
            'file_tugas.mimes' => 'Format file tidak didukung',
        ]);

        // Verifikasi bahwa mapel_id diajar oleh guru yang sedang login
        $allowedMapels = MataPelajaran::where('guru_id', auth()->id())->pluck('id')->toArray();
        if (!in_array($request->mapel_id, $allowedMapels)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk memperbarui tugas pada mata pelajaran ini.');
        }

        // Verifikasi bahwa kelas_id terhubung dengan jadwal pelajaran guru yang sedang login
        $allowedKelasIds = \App\Models\JadwalPelajaran::whereIn('mapel_id', $allowedMapels)->pluck('kelas_id')->unique()->toArray();
        if (!in_array($request->kelas_id, $allowedKelasIds)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Anda tidak memiliki wewenang untuk memperbarui tugas pada kelas ini.');
        }

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'deadline' => $request->deadline,
        ];

        if ($request->hasFile('file_tugas')) {
            // Hapus file lama jika ada
            if (!empty($tugas->file_tugas) && Storage::disk('public')->exists($tugas->file_tugas)) {
                Storage::disk('public')->delete($tugas->file_tugas);
            }
            
            $file = $request->file('file_tugas');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $data['file_tugas'] = $file->storeAs('tugas', $filename, 'public');
        }

        $tugas->update($data);

        return redirect()->route('guru.tugas.index')
                        ->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tugas = Tugas::where('guru_id', auth()->id())->findOrFail($id);

        // Hapus file dari storage jika ada
        if (!empty($tugas->file_tugas) && Storage::disk('public')->exists($tugas->file_tugas)) {
            Storage::disk('public')->delete($tugas->file_tugas);
        }

        $tugas->delete();

        return redirect()->route('guru.tugas.index')
                        ->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tugas = Tugas::where('guru_id', auth()->id())->with('kelas', 'mapel')->findOrFail($id);

        // Ambil semua siswa di kelas tugas tersebut
        $students = \App\Models\Siswa::where('kelas_id', $tugas->kelas_id)
            ->orderBy('nama', 'asc')
            ->get();

        // Ambil pengumpulan tugas untuk tugas ini
        $submissions = \App\Models\PengumpulanTugas::where('tugas_id', $tugas->id)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.tugas.show', compact('tugas', 'students', 'submissions'));
    }

    /**
     * Grade a student's submission.
     */
    public function gradeSubmission(Request $request, $submissionId)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:500',
        ]);

        $submission = \App\Models\PengumpulanTugas::findOrFail($submissionId);
        
        // Verifikasi bahwa tugas ini milik guru yang sedang login
        $tugas = Tugas::findOrFail($submission->tugas_id);
        if ($tugas->guru_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $submission->update([
            'nilai' => $request->nilai,
            'feedback' => $request->feedback,
        ]);

        // Kirim notifikasi in-app ke siswa terkait bahwa tugas telah dinilai
        $studentUser = \App\Models\User::where('siswa_id', $submission->siswa_id)->first();
        if ($studentUser) {
            \App\Models\AppNotification::sendNotification(
                $studentUser->id,
                'Tugas Dinilai: ' . $tugas->judul,
                'Tugas Anda telah dinilai oleh guru dengan skor ' . $request->nilai . '.',
                'tugas',
                route('siswa.tugas.index')
            );
        }

        return redirect()->back()->with('success', 'Tugas siswa berhasil dinilai!');
    }
}
