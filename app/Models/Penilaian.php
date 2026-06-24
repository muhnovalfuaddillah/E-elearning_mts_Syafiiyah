<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';

    protected $fillable = [
        'siswa_id',
        'mapel_id',
        'nilai_harian_1',
        'nilai_harian_2',
        'nilai_harian_3',
        'nilai_harian_4',
        'nilai_harian_5',
        'nilai_harian_6',
        'nilai_harian',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir'
    ];

    /**
     * Menghitung rata-rata nilai harian yang tidak bernilai null.
     */
    public function calculateNilaiHarian()
    {
        $fields = ['nilai_harian_1', 'nilai_harian_2', 'nilai_harian_3', 'nilai_harian_4', 'nilai_harian_5', 'nilai_harian_6'];
        $sum = 0;
        $count = 0;
        foreach ($fields as $field) {
            if ($this->$field !== null) {
                $sum += $this->$field;
                $count++;
            }
        }
        return $count > 0 ? ($sum / $count) : null;
    }

    /**
     * Boot function untuk menghitung nilai_akhir secara otomatis sebelum menyimpan.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($penilaian) {
            $penilaian->nilai_harian = $penilaian->calculateNilaiHarian();

            $harian = $penilaian->nilai_harian ?? 0;
            $uts = $penilaian->nilai_uts ?? 0;
            $uas = $penilaian->nilai_uas ?? 0;

            // Formula Bobot: Harian 40%, UTS 30%, UAS 30%
            $penilaian->nilai_akhir = ($harian * 0.40) + ($uts * 0.30) + ($uas * 0.30);
        });
    }

    /**
     * Relasi ke Siswa.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Mata Pelajaran.
     */
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
}
