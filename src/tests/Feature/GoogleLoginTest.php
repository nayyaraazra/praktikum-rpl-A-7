<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Google Login registers a new user with default buyer role when account is not found.
     */
    public function test_google_login_registers_new_user_if_not_found(): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'sub'   => '12345',
                'email' => 'newuser@example.com',
                'name'  => 'Google User Test',
            ], 200),
        ]);

        $payload = [
            'id_token' => 'some_valid_google_token',
        ];

        $response = $this->postJson('/api/auth/google', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id_user',
                        'name',
                        'email',
                        'google_id',
                        'roles',
                    ],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email'     => 'newuser@example.com',
            'google_id' => '12345',
            'name'      => 'Google User Test',
        ]);

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertEquals(['buyer'], $user->roles);
        $this->assertNull($user->phone_number);
        $this->assertNull($user->password);
    }

    /**
     * Test Google Login automatically links accounts with the same email.
     */
    public function test_google_login_links_existing_user_with_same_email(): void
    {
        // Setup existing user
        $user = User::create([
            'name'         => 'Existing Budi',
            'email'        => 'budi@example.com',
            'phone_number' => '081234567890',
            'password'     => bcrypt('secretpassword'),
            'roles'        => ['buyer'],
        ]);

        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'sub'   => '99999',
                'email' => 'budi@example.com',
                'name'  => 'Budi Google Name',
            ], 200),
        ]);

        $payload = [
            'id_token' => 'some_other_google_token',
        ];

        $response = $this->postJson('/api/auth/google', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        // Check if database updated google_id and did not duplicate user
        $this->assertEquals(1, User::where('email', 'budi@example.com')->count());

        $updatedUser = User::where('email', 'budi@example.com')->first();
        $this->assertEquals('99999', $updatedUser->google_id);
        $this->assertEquals('Existing Budi', $updatedUser->name); // Keep existing name
        $this->assertEquals('081234567890', $updatedUser->phone_number); // Keep existing phone number
    }

    /**
     * Test Google Login returns 401 for an invalid token.
     */
    public function test_google_login_fails_with_invalid_token(): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response([
                'error'             => 'invalid_token',
                'error_description' => 'Invalid Value',
            ], 400),
        ]);

        $payload = [
            'id_token' => 'invalid_google_token_here',
        ];

        $response = $this->postJson('/api/auth/google', $payload);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }
}
