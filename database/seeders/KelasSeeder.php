<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            // Kelas 10
            [
                'kode_kelas' => 'KLS-10-MIPA1',
                'nama_kelas' => 'MIPA 1',
                'tingkat' => '10',
                'jurusan' => 'MIPA',
                'deskripsi' => 'Kelas X Matematika dan Ilmu Pengetahuan Alam 1'
            ],
            [
                'kode_kelas' => 'KLS-10-MIPA2',
                'nama_kelas' => 'MIPA 2',
                'tingkat' => '10',
                'jurusan' => 'MIPA',
                'deskripsi' => 'Kelas X Matematika dan Ilmu Pengetahuan Alam 2'
            ],
            [
                'kode_kelas' => 'KLS-10-IPS1',
                'nama_kelas' => 'IPS 1',
                'tingkat' => '10',
                'jurusan' => 'IPS',
                'deskripsi' => 'Kelas X Ilmu Pengetahuan Sosial 1'
            ],
            [
                'kode_kelas' => 'KLS-10-IPS2',
                'nama_kelas' => 'IPS 2',
                'tingkat' => '10',
                'jurusan' => 'IPS',
                'deskripsi' => 'Kelas X Ilmu Pengetahuan Sosial 2'
            ],
            [
                'kode_kelas' => 'KLS-10-AGAMA',
                'nama_kelas' => 'Keagamaan',
                'tingkat' => '10',
                'jurusan' => 'Keagamaan',
                'deskripsi' => 'Kelas X Peminatan Ilmu-Ilmu Keagamaan'
            ],
            [
                'kode_kelas' => 'KLS-10-BAHASA',
                'nama_kelas' => 'Bahasa',
                'tingkat' => '10',
                'jurusan' => 'Bahasa',
                'deskripsi' => 'Kelas X Peminatan Bahasa dan Budaya'
            ],

            // Kelas 11
            [
                'kode_kelas' => 'KLS-11-MIPA1',
                'nama_kelas' => 'MIPA 1',
                'tingkat' => '11',
                'jurusan' => 'MIPA',
                'deskripsi' => 'Kelas XI Matematika dan Ilmu Pengetahuan Alam 1'
            ],
            [
                'kode_kelas' => 'KLS-11-MIPA2',
                'nama_kelas' => 'MIPA 2',
                'tingkat' => '11',
                'jurusan' => 'MIPA',
                'deskripsi' => 'Kelas XI Matematika dan Ilmu Pengetahuan Alam 2'
            ],
            [
                'kode_kelas' => 'KLS-11-IPS1',
                'nama_kelas' => 'IPS 1',
                'tingkat' => '11',
                'jurusan' => 'IPS',
                'deskripsi' => 'Kelas XI Ilmu Pengetahuan Sosial 1'
            ],
            [
                'kode_kelas' => 'KLS-11-IPS2',
                'nama_kelas' => 'IPS 2',
                'tingkat' => '11',
                'jurusan' => 'IPS',
                'deskripsi' => 'Kelas XI Ilmu Pengetahuan Sosial 2'
            ],
            [
                'kode_kelas' => 'KLS-11-AGAMA',
                'nama_kelas' => 'Keagamaan',
                'tingkat' => '11',
                'jurusan' => 'Keagamaan',
                'deskripsi' => 'Kelas XI Peminatan Ilmu-Ilmu Keagamaan'
            ],
            [
                'kode_kelas' => 'KLS-11-BAHASA',
                'nama_kelas' => 'Bahasa',
                'tingkat' => '11',
                'jurusan' => 'Bahasa',
                'deskripsi' => 'Kelas XI Peminatan Bahasa dan Budaya'
            ],

            // Kelas 12
            [
                'kode_kelas' => 'KLS-12-MIPA1',
                'nama_kelas' => 'MIPA 1',
                'tingkat' => '12',
                'jurusan' => 'MIPA',
                'deskripsi' => 'Kelas XII Matematika dan Ilmu Pengetahuan Alam 1'
            ],
            [
                'kode_kelas' => 'KLS-12-MIPA2',
                'nama_kelas' => 'MIPA 2',
                'tingkat' => '12',
                'jurusan' => 'MIPA',
                'deskripsi' => 'Kelas XII Matematika dan Ilmu Pengetahuan Alam 2'
            ],
            [
                'kode_kelas' => 'KLS-12-IPS1',
                'nama_kelas' => 'IPS 1',
                'tingkat' => '12',
                'jurusan' => 'IPS',
                'deskripsi' => 'Kelas XII Ilmu Pengetahuan Sosial 1'
            ],
            [
                'kode_kelas' => 'KLS-12-IPS2',
                'nama_kelas' => 'IPS 2',
                'tingkat' => '12',
                'jurusan' => 'IPS',
                'deskripsi' => 'Kelas XII Ilmu Pengetahuan Sosial 2'
            ],
            [
                'kode_kelas' => 'KLS-12-AGAMA',
                'nama_kelas' => 'Keagamaan',
                'tingkat' => '12',
                'jurusan' => 'Keagamaan',
                'deskripsi' => 'Kelas XII Peminatan Ilmu-Ilmu Keagamaan'
            ],
            [
                'kode_kelas' => 'KLS-12-BAHASA',
                'nama_kelas' => 'Bahasa',
                'tingkat' => '12',
                'jurusan' => 'Bahasa',
                'deskripsi' => 'Kelas XII Peminatan Bahasa dan Budaya'
            ],
        ];

        foreach ($classes as $class) {
            Kelas::updateOrCreate(
                ['kode_kelas' => $class['kode_kelas']],
                [
                    'nama_kelas' => $class['nama_kelas'],
                    'tingkat' => $class['tingkat'],
                    'jurusan' => $class['jurusan'],
                    'deskripsi' => $class['deskripsi'],
                ]
            );
        }
    }
}
