<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration of a new user.
     */
    public function test_successful_registration(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'phone_number'          => '081234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'buyer',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Akun berhasil dibuat.',
            ]);

        $this->assertDatabaseHas('users', [
            'email'        => 'john@example.com',
            'phone_number' => '081234567890',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertEquals(['buyer'], $user->roles);
    }

    /**
     * Test registering the same role with the same email fails.
     */
    public function test_register_same_role_fails(): void
    {
        // First registration
        $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'phone_number'          => '081234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'buyer',
        ]);

        // Second registration with the same role
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe Second',
            'email'                 => 'john@example.com',
            'phone_number'          => '081234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'buyer',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Email sudah terdaftar untuk peran ini.',
            ]);
    }

    /**
     * Test registering the other role with the same email succeeds and adds the role.
     */
    public function test_register_other_role_appends_role(): void
    {
        // First registration as buyer
        $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'phone_number'          => '081234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'buyer',
        ]);

        // Second registration as seller (same email, same phone)
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'phone_number'          => '081234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'seller',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Akun berhasil dibuat.',
            ]);

        $user = User::where('email', 'john@example.com')->first();
        // Check that roles has both values
        $this->assertEquals(['buyer', 'seller'], $user->roles);
    }

    /**
     * Test registering with an already used phone number by another email fails.
     */
    public function test_register_with_duplicate_phone_fails(): void
    {
        // First user
        $this->postJson('/api/auth/register', [
            'name'                  => 'User One',
            'email'                 => 'one@example.com',
            'phone_number'          => '081234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'buyer',
        ]);

        // Second user with different email but same phone number
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'User Two',
            'email'                 => 'two@example.com',
            'phone_number'          => '081234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'buyer',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Nomor telepon sudah terdaftar.',
            ]);
    }
}
