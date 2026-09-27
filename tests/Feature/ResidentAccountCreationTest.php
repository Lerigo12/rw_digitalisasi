<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResidentAccountCreationTest extends TestCase
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
        $this->family = Family::create(['rt_id' => $this->rt->id, 'family_number' => '3201010101010001', 'address' => 'Jl. Merdeka No. 1']);

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

    public function test_super_admin_can_add_resident_and_user_account_simultaneously(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('residents.store'), [
            'family_id' => $this->family->id,
            'nik' => '3201010101010099',
            'full_name' => 'Bambang Pamungkas',
            'birth_place' => 'Bandung',
            'birth_date' => '1985-06-10',
            'gender' => 'L',
            'religion' => 'Islam',
            'occupation' => 'Pegawai',
            'phone' => '081234567890',
            'status' => 'active',
            'verification_status' => 'verified',
            'email' => 'bambang@test.com',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect(route('residents.index'));
        $response->assertSessionHas('success');

        // Verify resident created
        $this->assertDatabaseHas('residents', [
            'full_name' => 'Bambang Pamungkas',
        ]);

        $resident = Resident::where('full_name', 'Bambang Pamungkas')->first();
        $this->assertNotNull($resident);

        // Verify user account created and linked
        $this->assertDatabaseHas('users', [
            'email' => 'bambang@test.com',
            'resident_id' => $resident->id,
        ]);

        $user = User::where('email', 'bambang@test.com')->first();
        $this->assertTrue($user->hasRole('resident'));

        // Test login with newly created account
        Auth::logout();
        $loginResponse = $this->post('/login', [
            'email' => 'bambang@test.com',
            'password' => 'secret1234',
        ]);

        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
