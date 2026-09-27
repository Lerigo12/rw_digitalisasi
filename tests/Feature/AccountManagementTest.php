<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createSuperAdmin(): User
    {
        $role = Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $user->roles()->attach($role->id, ['scope_type' => 'global', 'scope_id' => null]);

        return $user;
    }

    public function test_super_admin_can_create_bendahara_account_with_rt_scope()
    {
        $admin = $this->createSuperAdmin();

        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $roleBendahara = Role::create(['name' => 'Bendahara RT', 'slug' => 'bendahara-rt']);
        Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $response = $this->actingAs($admin)->post(route('accounts.store'), [
            'name' => 'Bendahara RT 001',
            'email' => 'bendahara@test.com',
            'password' => 'password123',
            'role_id' => $roleBendahara->id,
            'scope_type' => 'rt',
            'scope_id' => $rt->id,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseHas('users', ['email' => 'bendahara@test.com']);

        $user = User::where('email', 'bendahara@test.com')->first();
        $this->assertTrue($user->hasRole('bendahara-rt', 'rt', $rt->id));
    }

    public function test_super_admin_can_create_resident_account()
    {
        $admin = $this->createSuperAdmin();

        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $response = $this->actingAs($admin)->post(route('accounts.store'), [
            'name' => 'Warga Biasa',
            'email' => 'warga@test.com',
            'password' => 'password123',
            'role_id' => $roleResident->id,
            'scope_type' => 'rt',
            'scope_id' => $rt->id,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $user = User::where('email', 'warga@test.com')->first();
        $this->assertTrue($user->hasRole('resident', 'rt', $rt->id));
    }

    public function test_store_rejects_rt_scope_without_scope_id_and_shows_errors()
    {
        $admin = $this->createSuperAdmin();

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $response = $this->actingAs($admin)->from(route('accounts.index'))->post(route('accounts.store'), [
            'name' => 'Warga Tanpa Scope',
            'email' => 'warga2@test.com',
            'password' => 'password123',
            'role_id' => $roleResident->id,
            'scope_type' => 'rt',
            'scope_id' => null,
        ]);

        $response->assertSessionHasErrors('scope_id');
        $this->assertDatabaseMissing('users', ['email' => 'warga2@test.com']);
    }

    public function test_store_rejects_duplicate_email()
    {
        $admin = $this->createSuperAdmin();

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $response = $this->actingAs($admin)->from(route('accounts.index'))->post(route('accounts.store'), [
            'name' => 'Duplikat',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'role_id' => $roleResident->id,
            'scope_type' => 'global',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_index_page_displays_error_banner_when_validation_fails()
    {
        $admin = $this->createSuperAdmin();

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $this->actingAs($admin)->from(route('accounts.index'))->post(route('accounts.store'), [
            'name' => 'Password Pendek',
            'email' => 'pendek@test.com',
            'password' => '123',
            'role_id' => $roleResident->id,
            'scope_type' => 'global',
        ]);

        $response = $this->actingAs($admin)->get(route('accounts.index'));
        $response->assertOk();
    }

    public function test_super_admin_can_update_user_account()
    {
        $admin = $this->createSuperAdmin();

        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt1 = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $rt2 = Rt::create(['rw_id' => $rw->id, 'number' => '002', 'name' => 'RT 002', 'is_active' => true]);
        $roleBendahara = Role::create(['name' => 'Bendahara RT', 'slug' => 'bendahara-rt']);
        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $target = User::create([
            'name' => 'Bendahara Lama',
            'email' => 'bendahara@test.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);
        $target->roles()->attach($roleBendahara->id, ['scope_type' => 'rt', 'scope_id' => $rt1->id]);

        $response = $this->actingAs($admin)->put(route('accounts.update', $target), [
            'name' => 'Bendahara Baru',
            'email' => 'bendahara-baru@test.com',
            'password' => null,
            'role_id' => $roleResident->id,
            'scope_type' => 'rt',
            'scope_id' => $rt2->id,
        ]);

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Bendahara Baru', 'email' => 'bendahara-baru@test.com']);
        $this->assertFalse($target->fresh()->hasRole('bendahara-rt', 'rt', $rt1->id));
        $this->assertTrue($target->fresh()->hasRole('resident', 'rt', $rt2->id));
    }

    public function test_update_without_password_keeps_old_password()
    {
        $admin = $this->createSuperAdmin();

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);
        $target = User::create([
            'name' => 'Warga',
            'email' => 'warga@test.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);
        $target->roles()->attach($roleResident->id, ['scope_type' => 'global']);

        $oldHash = $target->password;

        $response = $this->actingAs($admin)->put(route('accounts.update', $target), [
            'name' => 'Warga',
            'email' => 'warga@test.com',
            'password' => null,
            'role_id' => $roleResident->id,
            'scope_type' => 'global',
        ]);

        $response->assertRedirect(route('accounts.index'));
        $this->assertSame($oldHash, $target->fresh()->password);
    }

    public function test_super_admin_can_delete_user_account()
    {
        $admin = $this->createSuperAdmin();

        $target = User::create([
            'name' => 'Warga Dihapus',
            'email' => 'hapus@test.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->delete(route('accounts.destroy', $target));

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_super_admin_cannot_delete_own_account()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->delete(route('accounts.destroy', $admin));

        $response->assertRedirect(route('accounts.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_super_admin_cannot_update_or_delete_accounts()
    {
        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $roleRtAdmin = Role::create(['name' => 'RT Admin', 'slug' => 'rt-admin']);

        $admin = $this->createSuperAdmin();

        $rtAdmin = User::create([
            'name' => 'Ketua RT',
            'email' => 'rt@test.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);
        $rtAdmin->roles()->attach($roleRtAdmin->id, ['scope_type' => 'rw', 'scope_id' => $rw->id]);

        $this->actingAs($rtAdmin)->put(route('accounts.update', $admin), [
            'name' => 'Hacked',
            'email' => 'admin@test.com',
            'role_id' => $roleRtAdmin->id,
            'scope_type' => 'global',
        ])->assertForbidden();

        $this->actingAs($rtAdmin)->delete(route('accounts.destroy', $admin))->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'name' => 'Super Admin']);
    }

    public function test_super_admin_sees_edit_page_with_current_values()
    {
        $admin = $this->createSuperAdmin();
        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);
        $target = User::create([
            'name' => 'Warga',
            'email' => 'warga@test.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);
        $target->roles()->attach($roleResident->id, ['scope_type' => 'global']);

        $response = $this->actingAs($admin)->get(route('accounts.edit', $target));

        $response->assertOk()->assertSee('Warga')->assertSee('warga@test.com');
    }
}
