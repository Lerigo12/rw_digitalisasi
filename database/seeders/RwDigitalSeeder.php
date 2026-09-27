<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use App\Models\Family;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RwDigitalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Akses penuh lintas wilayah'],
            ['name' => 'RW Admin / Ketua RW', 'slug' => 'rw-admin', 'description' => 'Pengurus tingkat RW'],
            ['name' => 'RT Admin / Ketua RT', 'slug' => 'rt-admin', 'description' => 'Pengurus tingkat RT'],
            ['name' => 'Bendahara RT', 'slug' => 'bendahara-rt', 'description' => 'Pengelola keuangan RT'],
            ['name' => 'Sekretaris RW', 'slug' => 'sekretaris-rw', 'description' => 'Pengelola persuratan RW'],
            ['name' => 'Warga', 'slug' => 'resident', 'description' => 'Warga biasa'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        // 2. Master RW & RT
        $rw = Rw::firstOrCreate(['code' => 'RW01'], [
            'name' => 'RW 01 Demangan',
            'code' => 'RW01',
            'address' => 'Jl. Demangan Baru No. 1',
            'is_active' => true,
        ]);

        $rt01 = Rt::firstOrCreate(['rw_id' => $rw->id, 'number' => '001'], [
            'rw_id' => $rw->id,
            'number' => '001',
            'name' => 'RT 001',
            'address' => 'Demangan RT 01',
            'is_active' => true,
        ]);

        $rt02 = Rt::firstOrCreate(['rw_id' => $rw->id, 'number' => '002'], [
            'rw_id' => $rw->id,
            'number' => '002',
            'name' => 'RT 002',
            'address' => 'Demangan RT 02',
            'is_active' => true,
        ]);

        // 3. Cash Accounts
        CashAccount::firstOrCreate(['rw_id' => $rw->id, 'rt_id' => null], [
            'rw_id' => $rw->id,
            'rt_id' => null,
            'name' => 'Kas Utama RW 01',
            'account_type' => 'bank',
            'opening_balance' => 5000000,
        ]);

        CashAccount::firstOrCreate(['rw_id' => null, 'rt_id' => $rt01->id], [
            'rw_id' => null,
            'rt_id' => $rt01->id,
            'name' => 'Kas RT 001',
            'account_type' => 'cash',
            'opening_balance' => 1500000,
        ]);

        // 4. Family & Resident & User Super Admin
        $superAdminUser = User::firstOrCreate(['email' => 'superadmin@rwdigital.test'], [
            'name' => 'Super Administrator',
            'email' => 'superadmin@rwdigital.test',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $superAdminUser->roles()->syncWithoutDetaching([
            $superAdminRole->id => ['scope_type' => 'global', 'scope_id' => null],
        ]);

        // 5. RW Admin
        $rwAdminUser = User::firstOrCreate(['email' => 'ketuarw@rwdigital.test'], [
            'name' => 'Bapak Ketua RW',
            'email' => 'ketuarw@rwdigital.test',
            'rw_id' => $rw->id,
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $rwAdminRole = Role::where('slug', 'rw-admin')->first();
        $rwAdminUser->roles()->syncWithoutDetaching([
            $rwAdminRole->id => ['scope_type' => 'rw', 'scope_id' => $rw->id],
        ]);
    }
}
