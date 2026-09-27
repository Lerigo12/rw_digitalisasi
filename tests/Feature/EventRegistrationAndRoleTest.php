<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Family;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRegistrationAndRoleTest extends TestCase
{
    use RefreshDatabase;

    private Rw $rw;

    private Rt $rt;

    private Resident $resident;

    private User $residentUser;

    private User $adminUser;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rw = Rw::create(['name' => 'RW 01', 'code' => 'RW01', 'is_active' => true]);
        $this->rt = Rt::create(['rw_id' => $this->rw->id, 'number' => '001', 'name' => 'RT 001', 'is_active' => true]);
        $family = Family::create(['rt_id' => $this->rt->id, 'family_number' => '3201010101010001', 'address' => 'Jl. Test']);

        $this->resident = Resident::create([
            'family_id' => $family->id,
            'full_name' => 'Warga Kegiatan',
            'nik' => '3201010101010001',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'status' => 'active',
        ]);

        $roleResident = Role::create(['name' => 'Warga', 'slug' => 'resident']);
        $roleAdmin = Role::create(['name' => 'RW Admin', 'slug' => 'rw-admin']);

        $this->residentUser = User::create([
            'resident_id' => $this->resident->id,
            'name' => 'Warga Kegiatan',
            'email' => 'wargakegiatan@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->residentUser->roles()->attach($roleResident->id, ['scope_type' => 'rt', 'scope_id' => $this->rt->id]);

        $this->adminUser = User::create([
            'name' => 'Admin RW',
            'email' => 'adminrw@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->adminUser->roles()->attach($roleAdmin->id, ['scope_type' => 'rw', 'scope_id' => $this->rw->id]);

        $this->event = Event::create([
            'created_by' => $this->adminUser->id,
            'title' => 'Kerja Bakti Massal',
            'description' => 'Membersihkan lingkungan RT/RW',
            'location' => 'Lapangan Utama',
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHours(3),
            'registration_enabled' => true,
            'status' => 'upcoming',
        ]);
    }

    public function test_resident_cannot_access_event_creation(): void
    {
        $response = $this->actingAs($this->residentUser)->get(route('events.create'));
        $response->assertStatus(403);

        $postResponse = $this->actingAs($this->residentUser)->post(route('events.store'), [
            'title' => 'Illegal Event',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHour(),
            'registration_enabled' => 1,
        ]);
        $postResponse->assertStatus(403);
    }

    public function test_admin_can_access_event_creation(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('events.create'));
        $response->assertStatus(200);
    }

    public function test_resident_can_register_and_count_updates(): void
    {
        $response = $this->actingAs($this->residentUser)->post(route('events.register', $this->event->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $this->event->id,
            'resident_id' => $this->resident->id,
            'status' => 'registered',
        ]);

        $indexResponse = $this->actingAs($this->residentUser)->get(route('events.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Terdaftar');
        $indexResponse->assertSee('1 Warga');
    }

    public function test_resident_cannot_register_twice(): void
    {
        EventRegistration::create([
            'event_id' => $this->event->id,
            'resident_id' => $this->resident->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($this->residentUser)->post(route('events.register', $this->event->id));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Anda sudah terdaftar pada kegiatan ini.');
    }

    public function test_resident_can_cancel_registration(): void
    {
        EventRegistration::create([
            'event_id' => $this->event->id,
            'resident_id' => $this->resident->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($this->residentUser)->post(route('events.cancel', $this->event->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $this->event->id,
            'resident_id' => $this->resident->id,
            'status' => 'cancelled',
        ]);
    }
}
