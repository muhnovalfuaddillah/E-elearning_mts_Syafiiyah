<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kelas_id',
        'mapel_id',
        'user_id',
        'file_materi',
        'link_video',
        'tipe'
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
     * Relasi ke User (Admin / Guru pengunggah).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Guru (alias dari user).
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor untuk file_path (alias dari file_materi).
     */
    public function getFilePathAttribute()
    {
        return $this->file_materi;
    }
}
