<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaKenaikanKelasTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_promote_students_to_new_class(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $kelasAsal = Kelas::create([
            'kode_kelas' => 'KLS-7A',
            'nama_kelas' => '7A',
            'tingkat' => 7,
            'jurusan' => 'Umum',
            'deskripsi' => 'Kelas awal',
        ]);

        $kelasTujuan = Kelas::create([
            'kode_kelas' => 'KLS-8A',
            'nama_kelas' => '8A',
            'tingkat' => 8,
            'jurusan' => 'Umum',
            'deskripsi' => 'Kelas tujuan',
        ]);

        $siswa = Siswa::create([
            'nis' => '1001',
            'nisn' => '9001',
            'nama' => 'Budi',
            'kelas_id' => $kelasAsal->id,
            'jenis_kelamin' => 'L',
            'telp' => '0812',
            'alamat' => 'Jl. Test',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.siswa.kenaikan-kelas'), [
            'kelas_asal_id' => $kelasAsal->id,
            'kelas_tujuan_id' => $kelasTujuan->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $siswa->refresh();
        $this->assertEquals($kelasTujuan->id, $siswa->kelas_id);
    }
}
