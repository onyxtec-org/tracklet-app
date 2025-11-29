<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\TestCaseBase;

class ProfileControllerTest extends TestCaseBase
{
    /** @test */
    public function authenticated_user_can_view_profile()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/profile');

        $response->assertStatus(200);
        $response->assertJsonPath('data.user.id', $this->adminUser->id);
    }

    /** @test */
    public function user_can_update_password_from_profile()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->putJson('/api/profile/password', [
            'old-password' => 'oldpassword',
            'new-password' => 'newpassword123',
            'new-password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function password_update_validates_old_password()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->putJson('/api/profile/password', [
            'old-password' => 'wrongpassword',
            'new-password' => 'newpassword123',
            'new-password_confirmation' => 'newpassword123'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['old-password']);
    }

    /** @test */
    public function password_update_validates_new_password_confirmation()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->putJson('/api/profile/password', [
            'old-password' => 'oldpassword',
            'new-password' => 'newpassword123',
            'new-password_confirmation' => 'different'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['new-password']);
    }

    /** @test */
    public function password_update_validates_new_password_is_different()
    {
        $user = User::factory()->forOrganization($this->organization->id)->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->putJson('/api/profile/password', [
            'old-password' => 'oldpassword',
            'new-password' => 'oldpassword',
            'new-password_confirmation' => 'oldpassword'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['new-password']);
    }
}

