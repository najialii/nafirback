<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Mentorship;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class MentorshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_mentorships()
    {
        Mentorship::factory()->count(3)->create();

        $response = $this->getJson('/api/mentorships');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_can_show_single_mentorship()
    {
        $mentorship = Mentorship::factory()->create();

        $response = $this->getJson('/api/mentorship/' . $mentorship->id);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $mentorship->id,
        ]);
    }

    public function test_can_search_mentorships()
    {
        $mentorships = Mentorship::factory()->create(['name' => 'laravel']);
        $response = $this->get('/api/search/mentorship/' . $mentorships->name);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'laravel']);

    }

    public function test_can_store_mentorship()
    {
        Sanctum::actingAs(User::factory()->create());
        $mentor = \App\Models\User::factory()->create();
        $department = Department::factory()->create();
        $data = [
            'name' => 'New Mentorship',
            'department_id' => $department->id,
            'mentor_id' => $mentor->id,
            'date' => ['Monday', 'Wednesday'],
            'availability' => ['10:00', '14:00'],
        ];


        $response = $this->postJson('/api/mentorship', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New Mentorship'
            ]);


        $this->assertDatabaseHas('mentorships', [
            'name' => 'New Mentorship',
            'mentor_id' => $mentor->id,
            'department_id' => $department->id,
        ]);
    }

    public function test_can_update_mentorship()
    {
        Sanctum::actingAs(User::factory()->create());

        $mentorship = Mentorship::factory()->create();

        $updateData = [
            'name' => 'Updated Mentorship',
            'bio' => 'Updated Bio',
            'specialization' => 'Business',
            'price' => 150,
            'days' => ['Tuesday'],
            'available_times' => ['11:00', '15:00'],
        ];

        $response = $this->putJson('/api/mentorship/' . $mentorship->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Mentorship updated successfully'
            ]);

        $this->assertDatabaseHas('mentorships', [
            'id' => $mentorship->id,
            'name' => 'Updated Mentorship',
        ]);
    }

    public function test_can_delete_mentorship()
    {
        Sanctum::actingAs(User::factory()->create());

        $mentorship = Mentorship::factory()->create();

        $response = $this->deleteJson('/api/mentorship/' . $mentorship->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Activity deleted successfully'
            ]);

        $this->assertDatabaseMissing('mentorships', [
            'id' => $mentorship->id,
        ]);
    }
}
