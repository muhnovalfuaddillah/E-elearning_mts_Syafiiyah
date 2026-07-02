<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;
use App\Models\User;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari guru default yang ada di database untuk dikaitkan
        $guru = User::where('role', 'guru')->first();
        $guruId = $guru ? $guru->id : null;

        $subjects = [
            // Mata Pelajaran Umum / Nasional
            ['kode_mapel' => 'MP-IND', 'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'MP-ING', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'MP-MAT', 'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'MP-PPK', 'nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan'],
            ['kode_mapel' => 'MP-IPA', 'nama_mapel' => 'Ilmu Pengetahuan Alam (IPA)'],
            ['kode_mapel' => 'MP-IPS', 'nama_mapel' => 'Ilmu Pengetahuan Sosial (IPS)'],
            ['kode_mapel' => 'MP-PJK', 'nama_mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
            ['kode_mapel' => 'MP-SBK', 'nama_mapel' => 'Seni Budaya'],
            ['kode_mapel' => 'MP-PRK', 'nama_mapel' => 'Prakarya / Informatika'],

            // Mata Pelajaran Ciri Khas Madrasah (Kemenag)
            ['kode_mapel' => 'MP-ARAB', 'nama_mapel' => 'Bahasa Arab'],
            ['kode_mapel' => 'MP-QURAN', 'nama_mapel' => 'Al-Qur\'an Hadis'],
            ['kode_mapel' => 'MP-AKIDAH', 'nama_mapel' => 'Akidah Akhlak'],
            ['kode_mapel' => 'MP-FIKIH', 'nama_mapel' => 'Fikih'],
            ['kode_mapel' => 'MP-SKI', 'nama_mapel' => 'Sejarah Kebudayaan Islam (SKI)'],
        ];

        foreach ($subjects as $subject) {
            MataPelajaran::updateOrCreate(
                ['kode_mapel' => $subject['kode_mapel']],
                [
                    'nama_mapel' => $subject['nama_mapel'],
                    'guru_id' => $guruId
                ]
            );
        }
    }
}
