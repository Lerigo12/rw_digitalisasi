<?php

namespace Tests\Feature;

use App\Models\CashAccount;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_role_and_multi_scope_user()
    {
        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt1 = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $rt2 = Rt::create(['rw_id' => $rw->id, 'number' => '002', 'name' => 'RT 002', 'is_active' => true]);

        $roleBendahara = Role::create(['name' => 'Bendahara RT', 'slug' => 'bendahara-rt']);
        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);

        $user = User::create([
            'name' => 'Bendahara Multi RT',
            'email' => 'bendahara@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $user->roles()->attach($roleBendahara->id, ['scope_type' => 'rt', 'scope_id' => $rt1->id]);
        $user->roles()->attach($roleBendahara->id, ['scope_type' => 'rt', 'scope_id' => $rt2->id]);
        $user->roles()->attach($roleResident->id, ['scope_type' => 'rt', 'scope_id' => $rt1->id]);

        $this->assertTrue($user->hasRole('bendahara-rt', 'rt', $rt1->id));
        $this->assertTrue($user->hasRole('bendahara-rt', 'rt', $rt2->id));
        $this->assertFalse($user->hasRole('bendahara-rt', 'rt', 999));
        $this->assertTrue($user->hasRole('resident', 'rt', $rt1->id));
    }

    public function test_cash_accounts_separation()
    {
        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);

        $rwCash = CashAccount::create([
            'rw_id' => $rw->id,
            'name' => 'Kas RW',
            'opening_balance' => 1000000,
        ]);

        $rtCash = CashAccount::create([
            'rt_id' => $rt->id,
            'name' => 'Kas RT',
            'opening_balance' => 500000,
        ]);

        $this->assertEquals($rw->id, $rwCash->rw_id);
        $this->assertNull($rwCash->rt_id);
        $this->assertEquals($rt->id, $rtCash->rt_id);
        $this->assertNull($rtCash->rw_id);
    }
}
