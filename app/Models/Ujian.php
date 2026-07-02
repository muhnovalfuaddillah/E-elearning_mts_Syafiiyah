<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujians';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kelas_id',
        'mapel_id',
        'guru_id',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'status',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'durasi' => 'integer',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function soals()
    {
        return $this->hasMany(UjianSoal::class, 'ujian_id');
    }

    public function ujianSiswas()
    {
        return $this->hasMany(UjianSiswa::class, 'ujian_id');
    }
}
