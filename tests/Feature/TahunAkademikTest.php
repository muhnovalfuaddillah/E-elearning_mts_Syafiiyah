<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class TahunAkademikTest extends TestCase
{
    public function test_admin_can_view_tahun_akademik_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/tahun-akademik');

        $response->assertOk();
        $response->assertSee('Tahun Akademik');
    }
}
