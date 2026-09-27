<?php

namespace Tests\Feature;

use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_asset()
    {
        $rw = Rw::factory()->create();
        $rt = Rt::factory()->create(['rw_id' => $rw->id]);
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $response = $this->post(route('assets.store'), [
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'asset_code' => 'ASSET001',
            'name' => 'Laptop',
            'quantity' => 5,
            'condition' => 'good',
        ]);

        $response->assertRedirect(route('assets.index'));
        $this->assertDatabaseHas('assets', ['asset_code' => 'ASSET001', 'name' => 'Laptop']);
    }
}
