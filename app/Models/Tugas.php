<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kelas_id',
        'mapel_id',
        'guru_id',
        'deadline',
        'file_tugas'
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    /**
     * Relasi ke Kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Mata Pelajaran.
     */
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    /**
     * Relasi ke User (Guru).
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id')->where('role', 'guru');
    }

    /**
     * Relasi ke PengumpulanTugas.
     */
    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class, 'tugas_id');
    }
}
