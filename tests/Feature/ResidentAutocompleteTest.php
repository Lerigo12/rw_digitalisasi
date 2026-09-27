<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentAutocompleteTest extends TestCase
{
    use RefreshDatabase;

    private Rw $rw;

    private Rt $rt;

    private Family $family;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $this->rt = Rt::create(['rw_id' => $this->rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $this->family = Family::create(['rt_id' => $this->rt->id, 'family_number' => '31201491237129', 'address' => 'Bekasi']);

        Role::create(['name' => 'Warga', 'slug' => 'resident']);
        $roleSuperAdmin = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->superAdmin->roles()->attach($roleSuperAdmin->id, ['scope_type' => 'global', 'scope_id' => null]);
    }

    public function test_can_search_family_via_autocomplete_api(): void
    {
        $response = $this->actingAs($this->superAdmin)->getJson(route('families.search', ['q' => '3120149123']));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'family_number' => '31201491237129',
            'rt_name' => 'RT 001',
            'address' => 'Bekasi',
        ]);
    }

    public function test_super_admin_can_add_resident_linked_to_searched_family(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('residents.store'), [
            'family_id' => $this->family->id,
            'nik' => '3201010101010098',
            'full_name' => 'Siti Aminah',
            'birth_place' => 'Bekasi',
            'birth_date' => '1992-05-15',
            'gender' => 'P',
            'religion' => 'Islam',
            'occupation' => 'Ibu Rumah Tangga',
            'phone' => '081298765432',
            'status' => 'active',
            'verification_status' => 'verified',
            'email' => 'siti@test.com',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect(route('residents.index'));
        $response->assertSessionHas('success');

        $resident = Resident::where('full_name', 'Siti Aminah')->first();
        $this->assertNotNull($resident);
        $this->assertEquals($this->family->id, $resident->family_id);

        $this->assertDatabaseHas('users', [
            'email' => 'siti@test.com',
            'resident_id' => $resident->id,
        ]);
    }

    public function test_invalid_family_id_fails_validation(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('residents.store'), [
            'family_id' => 99999,
            'nik' => '3201010101010098',
            'full_name' => 'Siti Aminah',
            'birth_place' => 'Bekasi',
            'birth_date' => '1992-05-15',
            'gender' => 'P',
            'status' => 'active',
            'verification_status' => 'verified',
            'email' => 'siti@test.com',
            'password' => 'secret1234',
        ]);

        $response->assertSessionHasErrors('family_id');
        $this->assertStringContainsString('Nomor KK tidak ditemukan', session('errors')->first('family_id'));
    }
}
