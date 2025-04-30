<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use App\Models\Department;
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_registers_a_user_successfully()
    {
        Storage::fake('public');
        $department = Department::factory()->create();
        Role::create(['name' => 'mentee']);
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'mentee',
            'department_id' => $department->id,
            'skills' => 'PHP, Laravel',
            'phone' => '1234567890',
            'exp_years' => 5,
            'country' => 'Sudan',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'name', 'email'],
                     'token',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function it_does_not_register_with_existing_email()
    {
        User::factory()->create(['email' => 'testsss@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => 'testsss@example.com',
            'password' => 'password',
            'role' => 'mentee',
            'department_id' => 2,
            'skills' => 'Vue.js',
            'phone' => '0987654321',
            'exp_years' => 2,
            'country' => 'Egypt',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'error' => 'Email alrady exists',
                 ]);
    }

    /** @test */
    public function it_logs_in_a_user_successfully()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user' => ['id', 'name', 'email'],
                     'token',
                 ]);
    }

    /** @test */
    public function it_fails_login_with_wrong_credentials()
    {
        $user = User::factory()->create([
            'email' => 'wronglogin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wronglogin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'invalid credentials',
                 ]);
    }

    /** @test */
    public function it_logs_out_successfully()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'You have been logged out',
                 ]);
    }
}
