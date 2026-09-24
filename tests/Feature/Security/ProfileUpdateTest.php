<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\SecurityTestCase;

class ProfileUpdateTest extends SecurityTestCase
{
    private function payload(): array
    {
        return ['name' => 'Updated player', 'email' => 'updated@example.test', 'password' => 'Test-only-password-123', 'current_password' => 'password1'];
    }

    public function test_guest_cannot_modify_an_account(): void
    {
        $user = User::factory()->create();
        $this->putJson('/api/users/'.$user->id, $this->payload())->assertUnauthorized();
        $this->assertModelExists($user);
    }

    public function test_user_cannot_update_someone_elses_account(): void
    {
        $owner = User::factory()->create();
        // Compare two persisted snapshots, including database defaults such as role.
        $original = $owner->fresh()->getAttributes();
        Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/users/'.$owner->id, $this->payload())->assertForbidden();
        $this->patchJson('/api/users/'.$owner->id, $this->payload())->assertForbidden();
        $this->assertSame($original, $owner->fresh()->getAttributes());
    }

    public function test_admin_cannot_change_another_users_password_through_profile_update(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->putJson('/api/users/'.$owner->id, $this->payload())->assertForbidden();
    }

    public function test_owner_can_update_profile_without_exposing_password_or_changing_role(): void
    {
        $owner = User::factory()->create();
        $role = $owner->fresh()->role;
        Sanctum::actingAs($owner);

        $response = $this->putJson('/api/users/'.$owner->id, $this->payload() + ['role' => 'admin']);
        $response->assertOk()->assertJsonPath('data.email', 'updated@example.test')
            ->assertJsonPath('data.id', $owner->id)->assertJsonPath('data.role', $role);
        $this->assertArrayNotHasKey('password', $response->json('data'));
        $this->assertArrayNotHasKey('current_password', $response->json('data'));
        $this->assertTrue(Hash::check('Test-only-password-123', $owner->fresh()->password));
        $this->assertSame($role, $owner->fresh()->role);
    }

    public function test_duplicate_email_is_rejected_without_modifying_account(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($owner);
        $payload = $this->payload();
        $payload['email'] = $other->email;

        $this->putJson('/api/users/'.$owner->id, $payload)->assertUnprocessable();
        $this->assertSame($owner->email, $owner->fresh()->email);
    }

    public function test_admin_profile_update_preserves_admin_role_in_response(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($owner);
        $this->putJson('/api/users/'.$owner->id, $this->payload())->assertOk()
            ->assertJsonPath('data.id', $owner->id)->assertJsonPath('data.role', 'admin');
    }

    public function test_published_name_form_accepts_correct_existing_password(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $this->putJson('/api/users/'.$owner->id, [
            'id' => $owner->id, 'name' => 'Renamed', 'email' => $owner->email, 'password' => 'password1',
        ])->assertOk()->assertJsonPath('data.name', 'Renamed');
        $this->assertTrue(Hash::check('password1', $owner->fresh()->password));
    }

    public function test_name_form_cannot_accidentally_replace_password_with_wrong_confirmation(): void
    {
        $owner = User::factory()->create();
        $original = $owner->fresh()->getAttributes();
        Sanctum::actingAs($owner);
        $this->putJson('/api/users/'.$owner->id, [
            'name' => 'Renamed', 'email' => $owner->email, 'password' => 'Wrong-confirmation-123',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->assertSame($original, $owner->fresh()->getAttributes());
    }

    public function test_password_change_requires_correct_current_password(): void
    {
        $owner = User::factory()->create();
        $original = $owner->fresh()->getAttributes();
        Sanctum::actingAs($owner);
        $payload = $this->payload();
        $payload['current_password'] = 'Wrong-current-password-123';
        $this->putJson('/api/users/'.$owner->id, $payload)->assertUnprocessable()
            ->assertJsonValidationErrors('current_password');
        $this->assertSame($original, $owner->fresh()->getAttributes());
    }

    public function test_omitting_current_password_does_not_allow_password_change(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $payload = $this->payload();
        unset($payload['current_password']);
        $this->putJson('/api/users/'.$owner->id, $payload)->assertUnprocessable()
            ->assertJsonValidationErrors('password');
        $this->assertTrue(Hash::check('password1', $owner->fresh()->password));
    }

    public function test_empty_current_password_does_not_bypass_confirmation(): void
    {
        $owner = User::factory()->create();
        Sanctum::actingAs($owner);
        $payload = $this->payload();
        $payload['current_password'] = '';
        $this->putJson('/api/users/'.$owner->id, $payload)->assertUnprocessable()
            ->assertJsonValidationErrors('current_password');
        $this->assertTrue(Hash::check('password1', $owner->fresh()->password));
    }





    public function test_own_account_endpoint_preserves_unwrapped_email_and_role(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($owner);
        $response = $this->getJson('/api/user')->assertOk()
            ->assertJsonPath('id', $owner->id)
            ->assertJsonPath('email', $owner->email)
            ->assertJsonPath('role', 'admin');
        $this->assertArrayNotHasKey('data', $response->json());
        $this->assertArrayNotHasKey('password', $response->json());
    }


    public function test_verified_user_can_still_login_and_read_own_account_with_bearer_token(): void
    {
        $owner = User::factory()->create(['password' => Hash::make('Test-only-password-123')]);
        $token = $this->postJson('/api/login', [
            'email' => $owner->email,
            'password' => 'Test-only-password-123',
        ])->assertOk()->json('token');

        $this->withToken($token)->getJson('/api/user')->assertOk()->assertJsonPath('email', $owner->email);
    }
}
