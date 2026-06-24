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
            // Mata Pelajaran Wajib
            ['kode_mapel' => 'MP-IND', 'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'MP-ING', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'MP-MAT', 'nama_mapel' => 'Matematika Wajib'],
            ['kode_mapel' => 'MP-AGM', 'nama_mapel' => 'Pendidikan Agama Islam'],
            ['kode_mapel' => 'MP-PPK', 'nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan'],
            ['kode_mapel' => 'MP-SEJ', 'nama_mapel' => 'Sejarah Indonesia'],
            ['kode_mapel' => 'MP-PJK', 'nama_mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
            ['kode_mapel' => 'MP-SBK', 'nama_mapel' => 'Seni Budaya'],
            ['kode_mapel' => 'MP-PKW', 'nama_mapel' => 'Prakarya dan Kewirausahaan'],

            // Peminatan MIPA (Matematika dan Ilmu Pengetahuan Alam)
            ['kode_mapel' => 'MP-FIS', 'nama_mapel' => 'Fisika'],
            ['kode_mapel' => 'MP-KIM', 'nama_mapel' => 'Kimia'],
            ['kode_mapel' => 'MP-BIO', 'nama_mapel' => 'Biologi'],
            ['kode_mapel' => 'MP-MTP', 'nama_mapel' => 'Matematika Peminatan'],

            // Peminatan IPS (Ilmu Pengetahuan Sosial)
            ['kode_mapel' => 'MP-GEO', 'nama_mapel' => 'Geografi'],
            ['kode_mapel' => 'MP-SOS', 'nama_mapel' => 'Sosiologi'],
            ['kode_mapel' => 'MP-EKO', 'nama_mapel' => 'Ekonomi'],
            ['kode_mapel' => 'MP-SJP', 'nama_mapel' => 'Sejarah Peminatan'],
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
