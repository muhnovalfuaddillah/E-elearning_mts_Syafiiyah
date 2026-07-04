<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    use HasFactory;

    protected $table = 'tahun_akademik';

    protected $fillable = [
        'nama_tahun',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'status_aktif' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status_aktif', true);
    }
}
