<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Models\JurnalMengajar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurnalRekapTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_jurnal_rekap_with_active_academic_year(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $activeYear = TahunAkademik::create([
            'nama_tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(6)->toDateString(),
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'kode_kelas' => 'KLS-7A',
            'nama_kelas' => '7A',
            'tingkat' => 7,
            'jurusan' => 'Umum',
            'deskripsi' => 'Kelas A',
        ]);

        $mapel = MataPelajaran::create([
            'kode_mapel' => 'MP-IND',
            'nama_mapel' => 'Bahasa Indonesia',
            'kkm' => 75,
            'deskripsi' => 'Mapel Bahasa',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.jurnal.rekap'));

        $response->assertOk();
        $response->assertSee('Tahun Ajaran 2026/2027');
        $response->assertSee('Yayasan Pendidikan Islam Syafiiyah');
        $response->assertSee('MTs Syafiiyah');
        $response->assertSee('Biro Pendidikan Yayasan');
    }

    public function test_guru_can_view_jurnal_rekap_with_active_academic_year(): void
    {
        $guru = User::factory()->create([
            'role' => 'guru',
        ]);

        $activeYear = TahunAkademik::create([
            'nama_tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(6)->toDateString(),
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'kode_kelas' => 'KLS-7A',
            'nama_kelas' => '7A',
            'tingkat' => 7,
            'jurusan' => 'Umum',
            'deskripsi' => 'Kelas A',
        ]);

        $mapel = MataPelajaran::create([
            'kode_mapel' => 'MP-IND',
            'nama_mapel' => 'Bahasa Indonesia',
            'kkm' => 75,
            'deskripsi' => 'Mapel Bahasa',
        ]);

        $this->actingAs($guru);

        $response = $this->get(route('guru.jurnal.rekap'));

        $response->assertOk();
        $response->assertSee('Tahun Ajaran 2026/2027');
        $response->assertSee('Yayasan Pendidikan Islam Syafiiyah');
        $response->assertSee('MTs Syafiiyah');
        $response->assertSee('Biro Pendidikan Yayasan');
    }

    public function test_admin_can_export_siswa_pdf_with_active_academic_year(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $activeYear = TahunAkademik::create([
            'nama_tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(6)->toDateString(),
            'status_aktif' => true,
        ]);

        $kelas = Kelas::create([
            'kode_kelas' => 'KLS-7A',
            'nama_kelas' => '7A',
            'tingkat' => 7,
            'jurusan' => 'Umum',
            'deskripsi' => 'Kelas A',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('laporan.siswa', [
            'kelas_id' => $kelas->id,
            'export' => 'pdf',
        ]));

        $response->assertOk();
        $response->assertSee('Tahun Ajaran 2026/2027');
        $response->assertSee('Laporan Rekapitulasi Data Siswa');
        $response->assertSee('Yayasan Pendidikan Islam Syafiiyah');
        $response->assertSee('MTs Syafiiyah');
        $response->assertSee('Biro Pendidikan Yayasan');
    }
}
