<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;

    protected $table = 'jurnal_mengajar';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mapel_id',
        'tanggal',
        'pertemuan_ke',
        'materi',
        'kegiatan',
        'catatan'
    ];

    /**
     * Relasi ke model User (Guru Pengampu).
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id')->where('role', 'guru');
    }

    /**
     * Relasi ke model Kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke model Mata Pelajaran.
     */
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
}
