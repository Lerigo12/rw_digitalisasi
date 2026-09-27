<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Family;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementPermissionTest extends TestCase
{
    use RefreshDatabase;

    private function createResidentUser(string $name): User
    {
        $rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $rt = Rt::create(['rw_id' => $rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $family = Family::create([
            'rt_id' => $rt->id,
            'address' => 'Jl. Anggrek No. 1',
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

    private function createStaffUser(string $roleSlug): User
    {
        $rw = Rw::firstOrCreate(['code' => 'RW01'], ['name' => 'RW 01', 'is_active' => true]);
        $rt = Rt::firstOrCreate(['number' => '001'], ['rw_id' => $rw->id, 'name' => 'RT 001', 'is_active' => true]);
        $user = User::create([
            'name' => 'Staff Test',
            'email' => 'staff_'.rand(10, 99).'@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'rt_id' => $rt->id,
            'rw_id' => $rw->id,
        ]);
        $role = Role::firstOrCreate(['slug' => $roleSlug], ['name' => ucfirst($roleSlug)]);
        $user->roles()->attach($role->id, ['scope_type' => 'rw', 'scope_id' => $rw->id]);

        return $user;
    }

    public function test_resident_can_view_announcements_and_detail_but_cannot_see_create_button(): void
    {
        $resident = $this->createResidentUser('Warga Announcement');
        $staff = $this->createStaffUser('rw-admin');

        $announcement = Announcement::create([
            'created_by' => $staff->id,
            'title' => 'Kerja Bakti Bersama',
            'body' => 'Diharapkan hadir pada hari Minggu.',
            'status' => 'published',
        ]);

        // 1. Index page
        $response = $this->actingAs($resident)->get(route('announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Kerja Bakti Bersama');
        $response->assertDontSee('Tambah Pengumuman');

        // 2. Show page
        $showResponse = $this->actingAs($resident)->get(route('announcements.show', $announcement->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Kerja Bakti Bersama');
        $showResponse->assertSee('Diharapkan hadir pada hari Minggu.');

        // 3. Create route attempt
        $createResponse = $this->actingAs($resident)->get(route('announcements.create'));
        $createResponse->assertStatus(403);

        // 4. Store route attempt
        $storeResponse = $this->actingAs($resident)->post(route('announcements.store'), [
            'title' => 'Pengumuman Palsu',
            'body' => 'Konten palsu',
            'status' => 'published',
        ]);
        $storeResponse->assertStatus(403);
    }

    public function test_staff_can_view_create_button_and_store_announcement(): void
    {
        $staff = $this->createStaffUser('rw-admin');

        // 1. Index page
        $response = $this->actingAs($staff)->get(route('announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Pengumuman');

        // 2. Create page
        $createResponse = $this->actingAs($staff)->get(route('announcements.create'));
        $createResponse->assertStatus(200);

        // 3. Store announcement
        $storeResponse = $this->actingAs($staff)->post(route('announcements.store'), [
            'title' => 'Jadwal Ronda Malam',
            'body' => 'Ronda malam mulai pukul 22.00',
            'status' => 'published',
        ]);
        $storeResponse->assertRedirect(route('announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'title' => 'Jadwal Ronda Malam',
            'status' => 'published',
            'created_by' => $staff->id,
        ]);
    }
}
