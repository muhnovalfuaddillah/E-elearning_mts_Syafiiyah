<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Kelas extends Model
{
    use HasFactory;
    
    protected $table = 'kelas';
    
    protected $fillable = [
        'kode_kelas',
        'nama_kelas',
        'tingkat',
        'jurusan',
        'deskripsi'
    ];
    
    // Relasi dengan Siswa
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    // Relasi dengan Jadwal Pelajaran
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'kelas_id');
    }
    
    // Accessor untuk nama lengkap kelas
    public function getNamaLengkapAttribute()
    {
        return $this->kode_kelas;
    }
    
    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kode_kelas', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}