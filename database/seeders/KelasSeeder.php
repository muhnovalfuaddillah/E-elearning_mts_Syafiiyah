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
            // Kelas 7
            [
                'kode_kelas' => 'KLS-7A',
                'nama_kelas' => '7-A',
                'tingkat' => '7',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas VII-A Umum'
            ],
            [
                'kode_kelas' => 'KLS-7B',
                'nama_kelas' => '7-B',
                'tingkat' => '7',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas VII-B Umum'
            ],
            [
                'kode_kelas' => 'KLS-7C',
                'nama_kelas' => '7-C',
                'tingkat' => '7',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas VII-C Umum'
            ],
            [
                'kode_kelas' => 'KLS-7-AGAMA',
                'nama_kelas' => '7-Keagamaan',
                'tingkat' => '7',
                'jurusan' => 'Keagamaan',
                'deskripsi' => 'Kelas VII Unggulan Keagamaan'
            ],

            // Kelas 8
            [
                'kode_kelas' => 'KLS-8A',
                'nama_kelas' => '8-A',
                'tingkat' => '8',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas VIII-A Umum'
            ],
            [
                'kode_kelas' => 'KLS-8B',
                'nama_kelas' => '8-B',
                'tingkat' => '8',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas VIII-B Umum'
            ],
            [
                'kode_kelas' => 'KLS-8C',
                'nama_kelas' => '8-C',
                'tingkat' => '8',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas VIII-C Umum'
            ],
            [
                'kode_kelas' => 'KLS-8-AGAMA',
                'nama_kelas' => '8-Keagamaan',
                'tingkat' => '8',
                'jurusan' => 'Keagamaan',
                'deskripsi' => 'Kelas VIII Unggulan Keagamaan'
            ],

            // Kelas 9
            [
                'kode_kelas' => 'KLS-9A',
                'nama_kelas' => '9-A',
                'tingkat' => '9',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas IX-A Umum'
            ],
            [
                'kode_kelas' => 'KLS-9B',
                'nama_kelas' => '9-B',
                'tingkat' => '9',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas IX-B Umum'
            ],
            [
                'kode_kelas' => 'KLS-9C',
                'nama_kelas' => '9-C',
                'tingkat' => '9',
                'jurusan' => 'Umum',
                'deskripsi' => 'Kelas IX-C Umum'
            ],
            [
                'kode_kelas' => 'KLS-9-AGAMA',
                'nama_kelas' => '9-Keagamaan',
                'tingkat' => '9',
                'jurusan' => 'Keagamaan',
                'deskripsi' => 'Kelas IX Unggulan Keagamaan'
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
