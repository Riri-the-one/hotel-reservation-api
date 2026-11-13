<?php

namespace Tests\Feature;

use App\Http\Controllers\CatalogueController;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private User $guest;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2027, 1, 1)->startOfDay());
        $this->guest = User::factory()->create();
        $type = RoomType::create(['name' => 'Demo', 'capacity' => 2, 'base_price' => '75.25']);
        $this->room = Room::create(['room_number' => '101', 'room_type_id' => $type->id, 'status' => 'disponible']);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'room_id' => $this->room->id, 'guest_name' => 'Demo Guest',
            'check_in' => '2027-01-10', 'check_out' => '2027-01-12',
            'guests' => 2, 'request_key' => (string) Str::uuid(),
        ], $overrides);
    }

    private function book(array $overrides = [])
    {
        return $this->actingAs($this->guest)->postJson('/reservations', $this->payload($overrides));
    }

    public function test_booking_computes_price_and_ignores_client_amount(): void
    {
        $this->book(['total_price' => '0.01', 'status' => 'checked_out', 'user_id' => 999])->assertCreated()->assertJsonPath('total_price', '150.50')->assertJsonPath('status', 'confirmed');
        $this->assertDatabaseHas('reservations', ['guest_name' => 'Demo Guest', 'guests' => 2, 'user_id' => $this->guest->id]);
    }

    public function test_nightly_seasonal_tariffs_take_precedence_over_base(): void
    {
        Tariff::create(['room_type_id' => $this->room->room_type_id, 'price' => '100.10', 'start_date' => '2027-01-11', 'end_date' => '2027-01-11']);
        $this->book()->assertCreated()->assertJsonPath('total_price', '175.35');
    }

    public function test_invalid_dates_capacity_and_keys_are_rejected(): void
    {
        foreach ([['check_in' => 'garbage'], ['check_in' => '2026-12-31'], ['check_out' => '2027-01-10'], ['check_out' => '2027-01-09'], ['check_out' => '2027-02-30'], ['guests' => 3], ['guests' => 0], ['request_key' => 'invalid']] as $bad) {
            $this->book($bad)->assertUnprocessable();
        }
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_overlap_including_containment_is_rejected_but_boundaries_are_free(): void
    {
        $this->book()->assertCreated();
        foreach ([['2027-01-09', '2027-01-11'], ['2027-01-11', '2027-01-13'], ['2027-01-09', '2027-01-13'], ['2027-01-10', '2027-01-12'], ['2027-01-10', '2027-01-11']] as [$in, $out]) {
            $this->book(['check_in' => $in, 'check_out' => $out])->assertUnprocessable();
        }
        $this->book(['check_in' => '2027-01-08', 'check_out' => '2027-01-10'])->assertCreated();
        $this->book(['check_in' => '2027-01-12', 'check_out' => '2027-01-13'])->assertCreated();
    }

    public function test_another_room_of_same_type_remains_available(): void
    {
        $other = Room::create(['room_number' => '102', 'room_type_id' => $this->room->room_type_id, 'status' => 'disponible']);
        $this->book()->assertCreated();
        $this->getJson('/api/reservations/check-availability?check_in=2027-01-10&check_out=2027-01-12')->assertOk()->assertJsonCount(1, 'available_rooms')->assertJsonPath('available_rooms.0.id', $other->id);
        $this->book(['room_id' => $other->id])->assertCreated();
    }

    public function test_repeated_request_is_idempotent_and_changed_payload_is_rejected(): void
    {
        $data = $this->payload();
        $first = $this->actingAs($this->guest)->postJson('/reservations', $data)->assertCreated();
        $this->postJson('/reservations', $data)->assertCreated()->assertJsonPath('id', $first->json('id'));
        $this->postJson('/reservations', array_replace($data, ['guests' => 1]))->assertConflict();
        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_cancellation_releases_inventory_and_cannot_be_repeated(): void
    {
        $id = $this->book()->json('id');
        $this->postJson("/reservations/$id/cancel")->assertOk();
        $this->postJson("/reservations/$id/cancel")->assertConflict();
        $this->book()->assertCreated();
    }

    public function test_cross_user_access_is_forbidden(): void
    {
        $id = $this->book()->json('id');
        $this->actingAs(User::factory()->create());
        $this->get("/reservations/$id/confirmation")->assertForbidden();
        $this->postJson("/reservations/$id/cancel")->assertForbidden();
        $this->getJson('/api/reservations')->assertOk()->assertExactJson([]);
        $this->postJson("/api/reservations/$id/check-in")->assertForbidden();
    }

    public function test_admin_routes_are_protected_and_lifecycle_is_atomic(): void
    {
        $id = $this->book(['check_in' => '2027-01-01', 'check_out' => '2027-01-03'])->json('id');
        $this->get('/admin/reservations')->assertForbidden();
        $this->getJson('/api/rooms')->assertForbidden();
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get('/admin/reservations')->assertOk();
        $this->postJson("/api/reservations/$id/check-out")->assertConflict();
        $this->postJson("/api/reservations/$id/check-in")->assertOk()->assertJsonPath('status', 'checked_in');
        $this->postJson("/api/reservations/$id/check-in")->assertConflict();
        $this->postJson("/api/reservations/$id/check-out")->assertOk()->assertJsonPath('status', 'checked_out');
        $this->postJson("/api/reservations/$id/check-out")->assertConflict();
    }

    public function test_cannot_check_in_early_or_cancel_on_arrival_day(): void
    {
        $id = $this->book()->json('id');
        $this->guest->forceFill(['is_admin' => true])->save();
        $this->postJson("/api/reservations/$id/check-in")->assertConflict();
        $this->travelTo(now()->setDate(2027, 1, 10));
        $this->postJson("/reservations/$id/cancel")->assertConflict();
    }

    public function test_maintenance_and_missing_price_roll_back_everything(): void
    {
        $before = $this->room->updated_at;
        $this->room->update(['status' => 'maintenance']);
        $this->book()->assertUnprocessable();
        $this->room->update(['status' => 'disponible']);
        $this->room->roomType->update(['base_price' => 0]);
        $this->book()->assertUnprocessable();
        $this->assertDatabaseCount('reservations', 0);
        $this->assertEquals($before, $this->room->fresh()->updated_at);
    }

    public function test_guests_cannot_book_and_debug_route_is_absent(): void
    {
        $this->postJson('/reservations', $this->payload())->assertUnauthorized();
        $this->get('/debug-inertia')->assertNotFound();
        $this->get('/admin/reservations')->assertRedirect('/login');
    }

    public function test_account_deletion_cannot_erase_active_bookings(): void
    {
        $this->book()->assertCreated();
        $this->deleteJson('/profile', ['password' => 'password'])->assertConflict();
        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_admin_cannot_delete_room_history_or_room_types_in_use(): void
    {
        $this->book()->assertCreated();
        $this->guest->forceFill(['is_admin' => true])->save();
        $this->deleteJson('/api/rooms/'.$this->room->id)->assertConflict();
        $this->deleteJson('/api/room-types/'.$this->room->room_type_id)->assertConflict();
        $this->assertDatabaseCount('reservations', 1);
    }

    public function test_internal_exception_details_are_never_returned_with_debug_disabled(): void
    {
        config(['app.debug' => false]);
        $this->mock(CatalogueController::class)
            ->shouldReceive('index')->andThrow(new \RuntimeException('sensitive-internal-database-password'));
        $this->getJson('/')->assertStatus(500)->assertJsonMissing(['message' => 'sensitive-internal-database-password'])->assertJsonMissingPath('file')->assertJsonMissingPath('trace');
    }

    public function test_registration_cannot_grant_admin_privileges(): void
    {
        $this->post('/register', ['name' => 'Demo', 'email' => 'role@example.test', 'password' => 'password', 'password_confirmation' => 'password', 'is_admin' => true])->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'role@example.test', 'is_admin' => false]);
    }
}
