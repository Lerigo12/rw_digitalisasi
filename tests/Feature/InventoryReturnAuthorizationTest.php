<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\InventoryLoan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryReturnAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrower_can_see_return_button_and_return_item(): void
    {
        $userA = User::factory()->create();
        $category = InventoryCategory::create(['nama' => 'Elektronik']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-001',
            'nama_barang' => 'Proyektor',
            'kategori_id' => $category->id,
            'jumlah' => 1,
            'satuan' => 'Unit',
            'kondisi' => 'Baik',
            'status' => 'Dipinjam',
        ]);

        $loan = InventoryLoan::create([
            'kode_peminjaman' => 'PIN-0001',
            'user_id' => $userA->id,
            'nama_peminjam' => $userA->name,
            'nomor_hp' => '08123456789',
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali_rencana' => now()->addDays(3)->toDateString(),
            'status' => 'Dipinjam',
        ]);

        $response = $this->actingAs($userA)->get(route('inventories.returns'));
        $response->assertStatus(200);
        $response->assertSee('Kembalikan');

        $responseStore = $this->actingAs($userA)->post(route('inventories.returns.store', $loan->id), [
            'kondisi_sesudah' => 'Baik',
        ]);

        $responseStore->assertRedirect(route('inventories.returns'));
        $this->assertDatabaseHas('inventory_loans', [
            'id' => $loan->id,
            'status' => 'Dikembalikan',
        ]);
    }

    public function test_other_user_cannot_see_return_button_and_gets_403_on_return_request(): void
    {
        $userA = User::factory()->create(['name' => 'Akun A']);
        $userB = User::factory()->create(['name' => 'Akun B']);

        $category = InventoryCategory::create(['nama' => 'Elektronik']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-002',
            'nama_barang' => 'Sound System',
            'kategori_id' => $category->id,
            'jumlah' => 1,
            'satuan' => 'Unit',
            'kondisi' => 'Baik',
            'status' => 'Dipinjam',
        ]);

        $loan = InventoryLoan::create([
            'kode_peminjaman' => 'PIN-0002',
            'user_id' => $userA->id,
            'nama_peminjam' => 'Akun A',
            'nomor_hp' => '08123456789',
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali_rencana' => now()->addDays(3)->toDateString(),
            'status' => 'Dipinjam',
        ]);

        $responseB = $this->actingAs($userB)->get(route('inventories.returns'));
        $responseB->assertStatus(200);
        $responseB->assertDontSee('Kembalikan');
        $responseB->assertSee('Sedang Dipinjam');

        $responseStore = $this->actingAs($userB)->post(route('inventories.returns.store', $loan->id), [
            'kondisi_sesudah' => 'Baik',
        ]);

        $responseStore->assertStatus(403);
        $this->assertDatabaseHas('inventory_loans', [
            'id' => $loan->id,
            'status' => 'Dipinjam',
        ]);
    }

    public function test_cannot_return_already_returned_loan(): void
    {
        $userA = User::factory()->create();
        $category = InventoryCategory::create(['nama' => 'Elektronik']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-003',
            'nama_barang' => 'Kamera',
            'kategori_id' => $category->id,
            'jumlah' => 1,
            'satuan' => 'Unit',
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $loan = InventoryLoan::create([
            'kode_peminjaman' => 'PIN-0003',
            'user_id' => $userA->id,
            'nama_peminjam' => $userA->name,
            'nomor_hp' => '08123456789',
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali_rencana' => now()->addDays(3)->toDateString(),
            'status' => 'Dikembalikan',
        ]);

        $responseStore = $this->actingAs($userA)->post(route('inventories.returns.store', $loan->id), [
            'kondisi_sesudah' => 'Baik',
        ]);

        $responseStore->assertSessionHasErrors();
    }

    public function test_guest_cannot_return_item(): void
    {
        $userA = User::factory()->create();
        $category = InventoryCategory::create(['nama' => 'Elektronik']);
        $inventory = Inventory::create([
            'kode_barang' => 'INV-004',
            'nama_barang' => 'Mic',
            'kategori_id' => $category->id,
            'jumlah' => 1,
            'satuan' => 'Unit',
            'kondisi' => 'Baik',
            'status' => 'Dipinjam',
        ]);

        $loan = InventoryLoan::create([
            'kode_peminjaman' => 'PIN-0004',
            'user_id' => $userA->id,
            'nama_peminjam' => $userA->name,
            'nomor_hp' => '08123456789',
            'barang_id' => $inventory->id,
            'jumlah' => 1,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali_rencana' => now()->addDays(3)->toDateString(),
            'status' => 'Dipinjam',
        ]);

        $responseStore = $this->post(route('inventories.returns.store', $loan->id), [
            'kondisi_sesudah' => 'Baik',
        ]);

        $responseStore->assertRedirect(route('login'));
    }
}
