<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianJawaban extends Model
{
    use HasFactory;

    protected $table = 'ujian_jawabans';

    protected $fillable = [
        'ujian_siswa_id',
        'ujian_soal_id',
        'jawaban',
        'is_benar',
    ];

    protected $casts = [
        'is_benar' => 'boolean',
    ];

    public function ujianSiswa()
    {
        return $this->belongsTo(UjianSiswa::class, 'ujian_siswa_id');
    }

    public function soal()
    {
        return $this->belongsTo(UjianSoal::class, 'ujian_soal_id');
    }
}
