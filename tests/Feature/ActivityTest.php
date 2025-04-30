<?php

use App\Models\Activity;
use App\Models\Department;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivitiesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function authUser()
    {
        $department = Department::factory()->create();

        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'isActive' => 1,
            'department_id' => $department->id,
        ]);

        $user->assignRole('admin');
        Sanctum::actingAs($user, ['*']);
        return $user;
    }

    public function test_can_list_activities()
    {
        Activity::factory()->count(3)->create();

        $response = $this->get('/api/activites');

        $response->assertStatus(200);
    }

    public function test_can_search_activities()
    {

            $activites = Activity::factory()->create(['name'=> 'laravel']);
            $response = $this->get('/api/activities/search/' . $activites->name);
            $response->assertStatus(200);

    }

    public function test_can_show_single_activity()
    {
        $activity = Activity::factory()->create();

        $response = $this->get('/api/activities/' . $activity->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $activity->id]);
    }

    public function test_can_store_activity()
{
    $this->authUser();
    Storage::fake('public');

    $user = \App\Models\User::factory()->create();
    Department::factory()->create(['id' => 1]);

    $data = [
        'name' => 'New Activity',
        'description' => 'Activity description',
        'date' => now()->toDateString(),
        'time' => now()->format('H:i:s'),
        'department_id' => 1,
        'type' => 'Workshop',
        'user_id' => $user->id,
        'img' => UploadedFile::fake()->image('activity.jpg'),
    ];

    $response = $this->postJson('/api/activities', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('activities', ['name' => 'New Activity']);
}


    public function test_can_update_activity()
    {
        $this->authUser();

        Department::factory()->create(['id' => 1]);
        $activity = Activity::factory()->create([
            'department_id' => 1,
        ]);

        $updateData = [
            'name' => 'Updated Activity',
            'description' => 'Updated description',
            'date' => now()->addDay()->toDateString(),
            'time' => now()->addHour()->toTimeString(),
            'department_id' => 1,
        ];

        $response = $this->putJson('/api/activities/' . $activity->id, $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('activities', ['name' => 'Updated Activity']);
    }

    public function test_can_delete_activity()
    {
        $this->authUser();

        $activity = Activity::factory()->create();

        $response = $this->deleteJson('/api/activities/' . $activity->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }
}
