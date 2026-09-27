<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryLoanFormIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function createResidentUser(string $name, string $phone, ?Rt $rt = null): User
    {
        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = $rt ?? Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $family = Family::create([
            'rt_id' => $rt->id,
            'address' => 'Jl. Mawar No. 1',
            'family_number_encrypted' => 'encrypted',
            'family_number_hash' => 'hash_'.rand(100, 999),
            'status' => 'Tetap',
        ]);
        $resident = Resident::create([
            'family_id' => $family->id,
            'full_name' => $name,
            'phone' => $phone,
            'gender' => 'L',
            'status' => 'Tetap',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'nik' => '3201'.rand(100000000000, 999999999999),
        ]);
        $user = User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)).rand(10, 99).'@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'resident_id' => $resident->id,
            'rt_id' => $rt->id,
            'rw_id' => $rw->id,
        ]);
        $roleResident = Role::firstOrCreate(['slug' => 'resident'], ['name' => 'Warga']);
        $user->roles()->attach($roleResident->id, ['scope_type' => 'rt', 'scope_id' => $rt->id]);

        return $user;
    }

    private function createAdminUser(string $roleSlug, ?Rt $rt = null): User
    {
        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = $rt ?? Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin'.rand(10, 99).'@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'rt_id' => $rt->id,
            'rw_id' => $rw->id,
        ]);
        $role = Role::firstOrCreate(['slug' => $roleSlug], ['name' => 'Admin']);
        $user->roles()->attach($role->id, ['scope_type' => $roleSlug === 'rt-admin' ? 'rt' : 'rw', 'scope_id' => $roleSlug === 'rt-admin' ? $rt->id : $rw->id]);

        return $user;
    }

    public function test_resident_loan_form_shows_profile_data_and_stores_correctly(): void
    {
        $user = $this->createResidentUser('Budi Santoso', '081234567890');
        $category = InventoryCategory::create(['nama' => 'Elektronik']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-100',
            'nama_barang' => 'Proyektor',
            'kategori_id' => $category->id,
            'jumlah' => 2,
            'satuan' => 'Unit',
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $response = $this->actingAs($user)->get(route('inventories.loans'));
        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('081234567890');

        $responsePost = $this->actingAs($user)->post(route('inventories.loans.store'), [
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-27',
            'tanggal_kembali_rencana' => '2026-09-30',
            'keperluan' => 'Rapat warga',
        ]);

        $responsePost->assertRedirect(route('inventories.loans'));
        $this->assertDatabaseHas('inventory_loans', [
            'user_id' => $user->id,
            'nama_peminjam' => 'Budi Santoso',
            'nomor_hp' => '081234567890',
            'barang_id' => $inventory->id,
            'status' => 'Dipinjam',
        ]);
    }

    public function test_admin_can_select_resident_from_dropdown(): void
    {
        $admin = $this->createAdminUser('rt-admin');
        $resident = Resident::create([
            'full_name' => 'Siti Rahma',
            'phone' => '089876543210',
            'gender' => 'P',
            'status' => 'Tetap',
            'birth_place' => 'Jakarta',
            'birth_date' => '1992-05-12',
            'nik' => '3201'.rand(100000000000, 999999999999),
            'family_id' => Family::create([
                'rt_id' => $admin->rt_id,
                'address' => 'Jl. Melati No. 2',
                'family_number_encrypted' => 'encrypted',
                'family_number_hash' => 'hash_siti'.rand(100, 999),
                'status' => 'Tetap',
            ])->id,
        ]);

        $category = InventoryCategory::create(['nama' => 'Peralatan']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-101',
            'nama_barang' => 'Tenda',
            'kategori_id' => $category->id,
            'jumlah' => 5,
            'satuan' => 'Buah',
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $response = $this->actingAs($admin)->get(route('inventories.loans'));
        $response->assertStatus(200);
        $response->assertSee('Siti Rahma');

        $responsePost = $this->actingAs($admin)->post(route('inventories.loans.store'), [
            'resident_id' => $resident->id,
            'barang_id' => $inventory->id,
            'jumlah' => 2,
            'tanggal_pinjam' => '2026-09-27',
            'tanggal_kembali_rencana' => '2026-09-30',
            'keperluan' => 'Acara RT',
        ]);

        $responsePost->assertRedirect(route('inventories.loans'));
        $this->assertDatabaseHas('inventory_loans', [
            'nama_peminjam' => 'Siti Rahma',
            'nomor_hp' => '089876543210',
            'barang_id' => $inventory->id,
            'jumlah' => 2,
        ]);
    }

    public function test_rt_admin_cannot_select_resident_outside_rt_scope(): void
    {
        $admin = $this->createAdminUser('rt-admin');

        $rw = Rw::first();
        $rtOther = Rt::create(['rw_id' => $rw->id, 'number' => '002', 'name' => 'RT 002', 'is_active' => true]);

        $residentOutside = Resident::create([
            'full_name' => 'Warga Luar',
            'phone' => '081111111111',
            'gender' => 'L',
            'status' => 'Tetap',
            'birth_place' => 'Bandung',
            'birth_date' => '1988-08-18',
            'nik' => '3201'.rand(100000000000, 999999999999),
            'family_id' => Family::create([
                'rt_id' => $rtOther->id,
                'address' => 'Jl. Kamboja No. 3',
                'family_number_encrypted' => 'encrypted',
                'family_number_hash' => 'hash_luar'.rand(100, 999),
                'status' => 'Tetap',
            ])->id,
        ]);

        $category = InventoryCategory::create(['nama' => 'Peralatan']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-102',
            'nama_barang' => 'Kursi',
            'kategori_id' => $category->id,
            'jumlah' => 10,
            'satuan' => 'Pcs',
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $responsePost = $this->actingAs($admin)->post(route('inventories.loans.store'), [
            'resident_id' => $residentOutside->id,
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-27',
            'tanggal_kembali_rencana' => '2026-09-30',
        ]);

        $responsePost->assertStatus(403);
    }
}
