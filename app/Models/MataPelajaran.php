<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'guru_id'
    ];

    /**
     * Relasi ke model User (Guru Pengampu).
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id')->where('role', 'guru');
    }

    // Relasi dengan Jadwal Pelajaran
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'mapel_id');
    }
}
