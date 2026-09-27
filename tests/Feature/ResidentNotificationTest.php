<?php

namespace Tests\Feature;

use App\Models\Family;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createResidentUser(string $name): User
    {
        $rw = Rw::firstOrCreate(['code' => 'RW01'], ['name' => 'RW 01', 'is_active' => true]);
        $rt = Rt::firstOrCreate(['number' => '001', 'rw_id' => $rw->id], ['name' => 'RT 001', 'is_active' => true]);
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

    public function test_resident_only_sees_their_own_notifications(): void
    {
        $userA = $this->createResidentUser('Warga A');
        $userB = $this->createResidentUser('Warga B');

        $userA->notify(new GeneralNotification('Notif A', 'Pesan khusus Warga A', 'general', '/dashboard'));
        $userB->notify(new GeneralNotification('Notif B', 'Pesan khusus Warga B', 'general', '/dashboard'));

        $this->actingAs($userA);
        $response = $this->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Notif A');
        $response->assertSee('Pesan khusus Warga A');
        $response->assertDontSee('Notif B');
        $response->assertDontSee('Pesan khusus Warga B');
    }

    public function test_resident_cannot_mark_as_read_other_users_notification(): void
    {
        $userA = $this->createResidentUser('Warga A');
        $userB = $this->createResidentUser('Warga B');

        $userB->notify(new GeneralNotification('Notif B', 'Pesan rahasia B', 'general', '/dashboard'));
        $notifB = $userB->notifications()->first();

        $this->actingAs($userA);
        $response = $this->post(route('notifications.read', $notifB->id));
        $response->assertStatus(404);

        $this->assertNull($notifB->fresh()->read_at);
    }

    public function test_resident_can_mark_notification_as_read_and_badge_updates(): void
    {
        $userA = $this->createResidentUser('Warga A');
        $userA->notify(new GeneralNotification('Tagihan Baru', 'Tagihan iuran telah dibuat', 'fee', '/fees'));
        $notif = $userA->notifications()->first();

        $this->assertEquals(1, $userA->unreadNotifications()->count());

        $this->actingAs($userA);
        $response = $this->post(route('notifications.read', $notif->id));
        $response->assertRedirect('/fees');

        $this->assertNotNull($notif->fresh()->read_at);
        $this->assertEquals(0, $userA->unreadNotifications()->count());
    }

    public function test_resident_can_mark_all_notifications_as_read(): void
    {
        $userA = $this->createResidentUser('Warga A');
        $userA->notify(new GeneralNotification('Notif 1', 'Pesan 1'));
        $userA->notify(new GeneralNotification('Notif 2', 'Pesan 2'));

        $this->assertEquals(2, $userA->unreadNotifications()->count());

        $this->actingAs($userA);
        $response = $this->post(route('notifications.read-all'));
        $response->assertRedirect();

        $this->assertEquals(0, $userA->unreadNotifications()->count());
    }
}
