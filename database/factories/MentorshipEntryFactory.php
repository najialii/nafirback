<?php
namespace Database\Factories;

use App\Models\Mentorship;
use App\Models\MentorshipReq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MentorshipEntry>
 */
class MentorshipEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'start_date'        => $this->faker->dateTimeBetween('now', '+1 year'),                    
            'duration'            => $this->faker->numberBetween(30, 60),                               
            'mentorship_id'       => Mentorship::factory(),                                              
            // 'accepted_request_id' => MentorshipReq::factory(),
            // 'accepted_request_id' => null,                                           
            'link'                => $this->faker->url(),                                                
            'status'              => $this->faker->randomElement(['pending', 'rejected', 'accepted']), 
        ];
    }
}
