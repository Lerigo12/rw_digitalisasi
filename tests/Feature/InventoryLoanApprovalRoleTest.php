<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryLoan;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryLoanApprovalRoleTest extends TestCase
{
    use RefreshDatabase;

    private function createResidentUser(string $name): User
    {
        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
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
            'phone' => '081234567890',
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

    private function createAdminUser(): User
    {
        $rw = Rw::firstOrCreate(['code' => 'RW01'], ['name' => 'RW 01', 'is_active' => true]);
        $rt = Rt::firstOrCreate(['number' => '001'], ['rw_id' => $rw->id, 'name' => 'RT 001', 'is_active' => true]);
        $user = User::create([
            'name' => 'Admin RW',
            'email' => 'admin_rw'.rand(10, 99).'@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'rt_id' => $rt->id,
            'rw_id' => $rw->id,
        ]);
        $role = Role::firstOrCreate(['slug' => 'rw-admin'], ['name' => 'RW Admin']);
        $user->roles()->attach($role->id, ['scope_type' => 'rw', 'scope_id' => $rw->id]);

        return $user;
    }

    public function test_resident_cannot_see_approve_button_and_cannot_approve_via_endpoint(): void
    {
        $residentUser = $this->createResidentUser('Warga Satu');
        $category = InventoryCategory::create(['nama' => 'Peralatan']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-200',
            'nama_barang' => 'Genset',
            'kategori_id' => $category->id,
            'jumlah' => 1,
            'satuan' => 'Unit',
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $loan = InventoryLoan::create([
            'kode_peminjaman' => 'PIN-2001',
            'user_id' => $residentUser->id,
            'nama_peminjam' => 'Warga Satu',
            'nomor_hp' => '081234567890',
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-27',
            'tanggal_kembali_rencana' => '2026-09-30',
            'status' => 'Dipinjam',
        ]);

        // 1. Check UI: resident should not see 'Setujui'
        $response = $this->actingAs($residentUser)->get(route('inventories.loans'));
        $response->assertStatus(200);
        $response->assertDontSee('Setujui');

        // 2. Try approving directly via PATCH endpoint: should be forbidden (403)
        $patchResponse = $this->actingAs($residentUser)->patch(route('inventories.loans.status', $loan->id), [
            'status' => 'Disetujui',
        ]);
        $patchResponse->assertStatus(403);

        $this->assertDatabaseHas('inventory_loans', [
            'id' => $loan->id,
            'status' => 'Dipinjam',
        ]);
    }

    public function test_admin_can_see_approve_button_and_approve_loan(): void
    {
        $residentUser = $this->createResidentUser('Warga Dua');
        $admin = $this->createAdminUser();

        $category = InventoryCategory::create(['nama' => 'Peralatan']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-201',
            'nama_barang' => 'Speaker',
            'kategori_id' => $category->id,
            'jumlah' => 1,
            'satuan' => 'Unit',
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $loan = InventoryLoan::create([
            'kode_peminjaman' => 'PIN-2002',
            'user_id' => $residentUser->id,
            'nama_peminjam' => 'Warga Dua',
            'nomor_hp' => '081234567890',
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-09-27',
            'tanggal_kembali_rencana' => '2026-09-30',
            'status' => 'Dipinjam',
        ]);

        // Admin sees Setujui
        $response = $this->actingAs($admin)->get(route('inventories.loans'));
        $response->assertStatus(200);
        $response->assertSee('Setujui');

        // Admin approves loan
        $patchResponse = $this->actingAs($admin)->patch(route('inventories.loans.status', $loan->id), [
            'status' => 'Disetujui',
        ]);
        $patchResponse->assertRedirect(route('inventories.loans'));

        $this->assertDatabaseHas('inventory_loans', [
            'id' => $loan->id,
            'status' => 'Disetujui',
        ]);

        // After approved, button is gone
        $responseAfter = $this->actingAs($admin)->get(route('inventories.loans'));
        $responseAfter->assertDontSee('Setujui');
    }
}
