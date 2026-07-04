<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        // Fitur pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan Kelas
        if ($request->has('kelas_id') && !empty($request->kelas_id)) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->has('jenis_kelamin') && !empty($request->jenis_kelamin)) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        if ($sortBy === 'kelas') {
            $query->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                  ->orderBy('kelas.nama_kelas', $sortOrder)
                  ->select('siswa.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $siswa = $query->paginate(5)->withQueryString();
        
        // Ambil semua data kelas untuk drop-down form & filter
        $kelas = Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();

        return view('admin.siswa.index', compact('siswa', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'nis' => 'required|unique:siswa,nis|max:20',
            'nisn' => 'nullable|unique:siswa,nisn|max:20',
            'nama' => 'required|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'telp' => 'nullable|max:20',
            'alamat' => 'nullable',
        ], [
            'nis.required' => 'NIS wajib diisi',
            'nis.unique' => 'NIS sudah digunakan',
            'nisn.unique' => 'NISN sudah digunakan',
            'nama.required' => 'Nama siswa wajib diisi',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
        ]);

        // Simpan data
        $siswa = Siswa::create($request->all());

        // Buat akun user otomatis
        User::create([
            'name' => $siswa->nama,
            'email' => $siswa->nis . '@syafiiyah.sch.id',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'siswa_id' => $siswa->id
        ]);

        return redirect()->route('admin.siswa.index')
                        ->with('success', 'Siswa dan akun user berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        // Validasi data
        $request->validate([
            'nis' => 'required|max:20|unique:siswa,nis,' . $id,
            'nisn' => 'nullable|max:20|unique:siswa,nisn,' . $id,
            'nama' => 'required|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'telp' => 'nullable|max:20',
            'alamat' => 'nullable',
        ], [
            'nis.required' => 'NIS wajib diisi',
            'nis.unique' => 'NIS sudah digunakan',
            'nisn.unique' => 'NISN sudah digunakan',
            'nama.required' => 'Nama siswa wajib diisi',
            'kelas_id.required' => 'Kelas wajib dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
        ]);

        $siswa->update($request->all());

        return redirect()->route('admin.siswa.index')
                        ->with('success', 'Siswa berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
                        ->with('success', 'Siswa berhasil dihapus!');
    }

    public function kenaikanKelas(Request $request)
    {
        $selectedSiswaIds = $request->input('siswa_ids');

        if (is_string($selectedSiswaIds) && $selectedSiswaIds !== '') {
            $selectedSiswaIds = array_values(array_filter(array_map('trim', explode(',', $selectedSiswaIds))));
            $request->merge(['siswa_ids' => $selectedSiswaIds]);
        } elseif (empty($selectedSiswaIds)) {
            $request->merge(['siswa_ids' => []]);
        }

        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'kelas_tujuan_id' => 'required|exists:kelas,id|different:kelas_asal_id',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ], [
            'kelas_asal_id.required' => 'Kelas asal wajib dipilih',
            'kelas_tujuan_id.required' => 'Kelas tujuan wajib dipilih',
            'kelas_tujuan_id.different' => 'Kelas tujuan harus berbeda dari kelas asal',
        ]);

        $query = Siswa::where('kelas_id', $request->kelas_asal_id);

        if (!empty($request->siswa_ids)) {
            $query->whereIn('id', $request->siswa_ids);
        }

        $updatedCount = $query->update([
            'kelas_id' => $request->kelas_tujuan_id,
        ]);

        ActivityLog::log('kenaikan_kelas', 'Memindahkan siswa dari kelas ' . $request->kelas_asal_id . ' ke kelas ' . $request->kelas_tujuan_id . ' sebanyak ' . $updatedCount . ' siswa');

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Kenaikan kelas berhasil dilakukan untuk ' . $updatedCount . ' siswa.');
    }

    /**
     * Download CSV template for Excel import.
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_siswa.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Header kolom template
        $columns = ['nis', 'nisn', 'nama', 'kode_kelas', 'jenis_kelamin', 'telp', 'alamat'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            
            // Tulis header dengan delimiter titik koma ';' (lebih cocok untuk region Indonesia di Microsoft Excel)
            fputcsv($file, $columns, ';');
            
            // Tambahkan baris contoh
            fputcsv($file, ['10001', '0098765432', 'Budi Santoso', 'KLS-10-MIPA1', 'L', '081234567890', 'Jl. Sudirman No. 12'], ';');
            fputcsv($file, ['10002', '0098765433', 'Rina Marlina', 'KLS-11-MIPA2', 'P', '085712345678', 'Jl. Gatot Subroto No. 45'], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import data from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ], [
            'file.required' => 'File CSV wajib diunggah',
            'file.mimes' => 'Format file harus berupa CSV (.csv atau .txt)'
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $errors = [];
        $rowCount = 0;
        $successCount = 0;

        DB::beginTransaction();

        try {
            if (($handle = fopen($filePath, 'r')) !== FALSE) {
                // Deteksi otomatis delimiter (koma ',' atau titik koma ';')
                $firstLine = fgets($handle);
                $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
                
                // Kembalikan kursor file ke awal
                rewind($handle);
                
                // Baca baris header
                $headers = fgetcsv($handle, 1000, $delimiter);
                
                $rowNum = 1;
                while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                    $rowNum++;
                    
                    // Lewati jika baris kosong
                    if (empty(array_filter($data))) {
                        continue;
                    }
                    
                    $rowCount++;
                    
                    if (count($data) < 5) {
                        $errors[] = "Baris {$rowNum}: Format kolom tidak sesuai. Minimal isi NIS, Nama, Kode Kelas, Jenis Kelamin.";
                        continue;
                    }
                    
                    $nis = trim($data[0] ?? '');
                    $nisn = trim($data[1] ?? '');
                    $nama = trim($data[2] ?? '');
                    $kodeKelas = trim($data[3] ?? '');
                    $jk = strtoupper(trim($data[4] ?? ''));
                    $telp = trim($data[5] ?? '');
                    $alamat = trim($data[6] ?? '');
                    
                    // Validasi data baris
                    if (empty($nis)) {
                        $errors[] = "Baris {$rowNum}: NIS tidak boleh kosong.";
                        continue;
                    }
                    if (empty($nama)) {
                        $errors[] = "Baris {$rowNum}: Nama tidak boleh kosong.";
                        continue;
                    }
                    if (empty($kodeKelas)) {
                        $errors[] = "Baris {$rowNum}: Kode kelas tidak boleh kosong.";
                        continue;
                    }
                    if (!in_array($jk, ['L', 'P'])) {
                        $errors[] = "Baris {$rowNum}: Jenis kelamin harus 'L' atau 'P' (ditemukan: '{$jk}').";
                        continue;
                    }
                    
                    // Cari kelas berdasarkan kode_kelas
                    $kelas = Kelas::where('kode_kelas', $kodeKelas)->first();
                    if (!$kelas) {
                        $errors[] = "Baris {$rowNum}: Kelas dengan kode '{$kodeKelas}' tidak terdaftar di sistem.";
                        continue;
                    }
                    
                    // Cek keunikan NIS
                    if (Siswa::where('nis', $nis)->exists()) {
                        $errors[] = "Baris {$rowNum}: NIS '{$nis}' sudah terdaftar untuk siswa lain.";
                        continue;
                    }
                    
                    // Cek keunikan NISN jika diisi
                    if (!empty($nisn) && Siswa::where('nisn', $nisn)->exists()) {
                        $errors[] = "Baris {$rowNum}: NISN '{$nisn}' sudah terdaftar untuk siswa lain.";
                        continue;
                    }
                    
                    // Simpan data
                    $siswa = Siswa::create([
                        'nis' => $nis,
                        'nisn' => empty($nisn) ? null : $nisn,
                        'nama' => $nama,
                        'kelas_id' => $kelas->id,
                        'jenis_kelamin' => $jk,
                        'telp' => empty($telp) ? null : $telp,
                        'alamat' => empty($alamat) ? null : $alamat,
                    ]);

                    // Buat akun user otomatis
                    User::create([
                        'name' => $siswa->nama,
                        'email' => $siswa->nis . '@syafiiyah.sch.id',
                        'password' => bcrypt('password'),
                        'role' => 'siswa',
                        'siswa_id' => $siswa->id
                    ]);
                    
                    $successCount++;
                }
                fclose($handle);
            }
            
            // Jika ada baris yang gagal validasi, batalkan semua transaksi database
            if (!empty($errors)) {
                DB::rollBack();
                return redirect()->back()
                                ->withErrors($errors)
                                ->with('error', 'Proses upload dibatalkan karena terdapat data yang tidak valid.');
            }
            
            DB::commit();
            return redirect()->route('admin.siswa.index')
                             ->with('success', "Berhasil mengimpor {$successCount} data siswa dari file Excel/CSV!");
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses file: ' . $e->getMessage());
        }
    }
}
